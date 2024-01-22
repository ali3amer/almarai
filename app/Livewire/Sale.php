<?php

namespace App\Livewire;

use App\Models\ClientDebt;
use App\Models\EmployeeDebt;
use App\Models\SupplierDebt;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Bank;
use App\Models\SaleDebt;
use App\Models\SaleDetail;
use Cassandra\Date;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Sale extends Component
{
    use LivewireAlert;
    use WithPagination;

    protected $listeners = [
        'cancelSale',
    ];

    public string $title = 'المبيعات';
    public int $id = 0;
    public int $bank_id = 0;
    public int $debtId = 0;
    public string $sale_date = '';
    public string $due_date = '';
    public bool $print = false;
    public string $buyer = 'client';
    public string $search = '';
    public Collection $sales;
    public Collection $clients;
    public Collection $banks;
    public string $productSearch = '';
    public string $clientSearch = '';

    public $amount = 0;
    public $total_amount = 0;
    public $paid = 0;
    public string $payment = 'cash';
    public string $bank = '';

    public array $currentClient = [];
    public array $oldQuantities = [];
    public array $currentProduct = [];
    public array $cart = [];
    public string $saleSearch = '';
    public float $remainder = 0;
    public float $currentBalance = 0;
    public bool $editMode = false;
    public array $currentSaleDebts = [];
    public array $currentSale = [];
    public Collection $saleDebts;
    public array $invoice = [];
    public $discount = 0;

    public function mount()
    {
        if (\App\Models\Client::count() == 0) {
            \App\Models\Client::create(['clientName' => "نقدي", 'phone' => "", 'initialBalance' => 0, 'startingDate' => session("date"), 'blocked' => false, 'cash' => true]);
        }
        if (\App\Models\Client::where("cash", true)->first() != null) {
            $this->currentClient = \App\Models\Client::where("cash", true)->first()->toArray();
        } else {
            $this->currentClient = \App\Models\Client::first()->toArray();
        }

        $client = SaleDebt::where('client_id', $this->currentClient['id'])->get();
        $this->currentBalance = $client->sum('debt') - $client->sum('paid') + $this->currentClient['initialBalance'];
        $this->banks = Bank::all();

        if ($this->banks->count() != 0) {
            $this->bank_id = $this->banks->first()->id;
        }
    }

    public function save()
    {
        if ($this->id == 0) {
            $sale = \App\Models\Sale::create([
                $this->buyer . '_id' => $this->currentClient['id'],
                'paid' => floatval($this->paid),
                'remainder' => $this->remainder,
                'discount' => floatval($this->discount),
                'total_amount' => $this->total_amount,
                'sale_date' => $this->sale_date,
                'user_id' => auth()->id(),
            ]);

            \App\Models\SaleDebt::create([
                $this->buyer . '_id' => $this->currentClient['id'],
                'paid' => 0,
                'debt' => $this->total_amount,
                'type' => 'debt',
                'bank' => $this->bank,
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'due_date' => $this->sale_date,
                'note' => 'تم البيع بالآجل بفاتورة #' . $sale['id'],
                'sale_id' => $sale['id'],
                'user_id' => auth()->id()
            ]);

            if ($this->paid != 0) {
                \App\Models\SaleDebt::create([
                    $this->buyer . '_id' => $this->currentClient['id'],
                    'paid' => floatval($this->paid),
                    'debt' => 0,
                    'type' => 'pay',
                    'bank' => $this->bank,
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                    'due_date' => $this->sale_date,
                    'note' => 'تم إستلام مبلغ',
                    'sale_id' => $sale['id'],
                    'user_id' => auth()->id()
                ]);
            }

            $this->currentBalance += $this->remainder;


            foreach ($this->cart as $item) {
                SaleDetail::create([
                    'sale_id' => $sale['id'],
                    'product_id' => $item['id'],
                    'quantity' => floatval($item['quantity']),
                    'price' => floatval($item['price']),
                ]);

                \App\Models\Product::where('id', $item['id'])->decrement('stock', floatval($item['quantity']));
            }
        }

        $this->showInvoice($sale['id']);

        $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

        $this->resetData();

    }

    public function showInvoice($id = null)
    {
        $this->invoice['id'] = $id;
        $this->invoice['type'] = 'sale';
        $this->invoice['date'] = $this->sale_date;
        $this->invoice['client'] = $this->currentClient[$this->buyer . 'Name'];
        $this->invoice['cart'] = $this->cart;
        $this->invoice['remainder'] = $this->remainder;
        $this->invoice['discount'] = floatval($this->discount);
        $this->invoice['paid'] = floatval($this->paid);
        $this->invoice['amount'] = floatval($this->amount);
        $this->invoice['total_amount'] = floatval($this->total_amount);
        $this->invoice['showMode'] = false;
        $this->dispatch('sale_created', $this->invoice);
    }

    public function chooseClient($client)
    {
        $this->currentClient = $client;
        $this->currentClient['blocked'] = $this->buyer != 'employee' ? $this->currentClient['blocked'] : false;
        $this->currentClient['cash'] = $this->buyer == "client" ? $this->currentClient['cash'] : false;
        if ($this->buyer == 'client') {
            $client = SaleDebt::where('client_id', $this->currentClient['id'])->get();
        } elseif ($this->buyer == 'supplier') {
            $client = SaleDebt::where('supplier_id', $this->currentClient['id'])->get();
        } elseif ($this->buyer == 'employee') {
            $client = SaleDebt::where('employee_id', $this->currentClient['id'])->get();
        }

        $this->currentBalance = $client->sum('debt') - $client->sum('paid') + $this->currentClient['initialBalance'];

    }

    public function chooseProduct($product)
    {
        if ($product["stock"] > 0) {
            $this->currentProduct = $product;
            $this->currentProduct['quantity'] = 1;
            $this->currentProduct['price'] = $product['sale_price'];
            $this->currentProduct['amount'] = $product['sale_price'];
            $this->productSearch = '';
        }

    }

    public function calcCurrentProduct()
    {
        $this->currentProduct['amount'] = floatval($this->currentProduct['price']) * floatval($this->currentProduct['quantity']);
    }


    public function addToCart()
    {
        if (!isset($this->cart[$this->currentProduct['id']])) {

            if ($this->currentProduct["quantity"] > $this->currentProduct["stock"]) {
                $this->confirm("العدد المطلوب من " . $this->currentProduct['productName'] . " غير متوفر لايوجد سوى " . $this->currentProduct['stock'], [
                    'toast' => false,
                    'showConfirmButton' => false,
                    'confirmButtonText' => 'موافق',
                    'onConfirmed' => "cancelSale",
                    'showCancelButton' => true,
                    'cancelButtonText' => 'إلغاء',
                    'confirmButtonColor' => '#dc2626',
                    'cancelButtonColor' => '#4b5563'
                ]);
            } else {
                $this->cart[$this->currentProduct['id']] = $this->currentProduct;

                $this->cart[$this->currentProduct['id']]['amount'] = floatval($this->currentProduct['price']) * floatval($this->currentProduct['quantity']);

                $this->amount += $this->cart[$this->currentProduct['id']]['amount'];
                if ($this->currentClient['id'] == 1 && $this->buyer == "client") {
                    $this->paid = $this->amount - $this->discount;
                }

            }

        } else {
            $this->amount -= $this->cart[$this->currentProduct['id']]['amount'];
            $this->cart[$this->currentProduct['id']]['quantity'] += floatval($this->currentProduct['quantity']);
            $this->cart[$this->currentProduct['id']]['amount'] = floatval($this->cart[$this->currentProduct['id']]['price']) * floatval($this->cart[$this->currentProduct['id']]['quantity']);
            $this->amount += $this->cart[$this->currentProduct['id']]['amount'];

        }
        $this->currentProduct = [];
        $this->calcRemainder();

    }

    public function deleteFromCart($id)
    {
        $this->amount -= $this->cart[$id]['amount'];


//        if ($this->paid) {
//            $this->paid = $this->amount;
//        }

        unset($this->cart[$id]);
        if (empty($this->cart)) {
            $this->remainder = 0;
            $this->paid = 0;
            $this->discount = 0;
        }
        $this->calcRemainder();

    }

    public function showSales()
    {
        $this->editMode = !$this->editMode;
    }

    public function getSale($sale)
    {
        $this->invoice['id'] = $sale['id'];
        $this->invoice['type'] = 'sale';
        $this->invoice['clientType'] = 'العميل';
        $this->invoice['date'] = $sale['sale_date'];
        $this->invoice['client'] = $this->currentClient[$this->buyer . 'Name'];
        $this->invoice['cart'] = SaleDetail::where('sale_id', $sale['id'])->join('products', 'products.id', '=', 'sale_details.product_id')->get()->toArray();
        $this->invoice['remainder'] = floatval($sale['remainder']);
        $this->invoice['paid'] = floatval($sale['paid']);
        $this->invoice['discount'] = floatval($sale['discount']);
        $this->invoice['amount'] = floatval($sale['total_amount']) + floatval($sale['discount']);
        $this->invoice['total_amount'] = $sale['total_amount'];
        $this->invoice['showMode'] = false;
        $this->dispatch('sale_created', $this->invoice);
    }

    public function deleteMessage($id)
    {
        $this->confirm("  هل توافق على إلغاء الفاتورة ؟", [
            'inputAttributes' => ["id" => $id],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "cancelSale",
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }

    public function cancelSale($data)
    {
        $id = $data['inputAttributes']['id'];

        $items = SaleDetail::where('sale_id', $id)->get();
        foreach ($items as $item) {
            \App\Models\Product::where('id', $item['product_id'])->increment('stock', floatval($item['quantity']));
            \App\Models\SaleDetail::where('id', $item['id'])->delete();
        }

        \App\Models\Sale::where('id', $id)->delete();

        SaleDebt::where("sale_id", $id)->where("type", "debt")->delete();
        $paid = SaleDebt::where("sale_id", $id)->where("type", "pay")->first();
        \App\Models\SaleDebt::create([
            $this->buyer . '_id' => $this->currentClient['id'],
            'paid' => $this->invoice['total_amount'],
            'debt' => 0,
            'type' => 'pay',
            'bank' => '',
            'payment' => 'cash',
            'bank_id' => null,
            'due_date' => $this->sale_date,
            'note' => 'تم إلغاء الفاتوره رقم #' . $this->invoice['id'],
            'sale_id' => $this->invoice['id'],
            'user_id' => auth()->id()
        ])->delete();


        if ($paid) {
            \App\Models\SaleDebt::create([
                $this->buyer . '_id' => $this->currentClient['id'],
                'paid' => 0,
                'debt' => $paid->paid,
                'type' => 'debt',
                'bank' => '',
                'payment' => 'cash',
                'bank_id' => null,
                'due_date' => $this->sale_date,
                'note' => 'تم إلغاء الفاتوره رقم #' . $this->invoice['id'],
                'sale_id' => $this->invoice['id'],
                'user_id' => auth()->id()
            ])->delete();
            $paid->delete();
        }

        $this->alert('success', 'تم الإلغاء بنجاح', ['timerProgressBar' => true]);

    }

    public function calcRemainder()
    {
        $this->total_amount = $this->amount - floatval($this->discount);
        if ($this->currentClient['cash'] && $this->buyer == "client") {
            $this->paid = $this->total_amount;
        } else {
            $this->remainder = floatval($this->total_amount) - floatval($this->paid);
        }
    }

    public function resetData($item = null)
    {
        $this->reset('currentProduct', 'cart', 'search', 'clientSearch', 'paid', 'remainder', 'total_amount', 'amount', 'discount', 'id', 'oldQuantities', $item);
    }

    public function render()
    {


        if (!empty($this->currentClient)) {
            $this->sales = \App\Models\Sale::where($this->buyer . '_id', $this->currentClient['id'])
                ->where('id', 'LIKE', '%' . $this->saleSearch . '%')->where('sale_date', 'LIKE', '%' . $this->saleSearch . '%')->latest()->get();
        }
        if ($this->sale_date == '') {
            $this->sale_date = session("date");
        }
        if ($this->buyer == 'client') {
            $this->clients = \App\Models\Client::where('clientName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        } elseif ($this->buyer == 'employee') {
            $this->clients = \App\Models\Employee::where('employeeName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        } elseif ($this->buyer == 'supplier') {
            $this->clients = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        }
        return view('livewire.sale', [
            'products' => \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->simplePaginate(10)
        ]);
    }
}
