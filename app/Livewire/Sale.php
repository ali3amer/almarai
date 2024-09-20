<?php

namespace App\Livewire;

use App\Models\ClientDebt;
use App\Models\EmployeeDebt;
use App\Models\People;
use App\Models\Service;
use App\Models\Setting;
use App\Models\SupplierDebt;
use Illuminate\Support\Facades\DB;
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
        'save'
    ];

    public string $title = 'المبيعات';
    public bool $show = false;

    public int $id = 0;
    public $bank_id = null;
    public $note = null;
    public int $debtId = 0;
    public string $due_date = '';
    public bool $print = false;
    public string $buyer = 'client';
    public string $search = '';
    public Collection $sales;
    public Collection $clients;
    public Collection $banks;
    public string $productSearch = '';
    public string $clientSearch = '';

    public $cost = 0;
    public $amount = 0;
    public $paid = 0;
    public string $payment = 'cash';
    public $bank = '';

    public array $currentClient = [];
    public array $oldQuantities = [];
    public array $currentProduct = [];
    public array $cart = [];
    public array $services = [];
    public string $saleSearch = '';
    public float $remainder = 0;
    public float $currentSalesBalance = 0;
    public bool $editMode = false;
    public Collection $saleDebts;
    public array $invoice = [];
    public $discount = 0;
    public Setting $settings;
    public $serviceName = "";
    public $serviceAmount = 0;
    public $totalServices = 0;

    public function mount()
    {
        if (Setting::count() != 0) {
            $this->settings = Setting::first();
        } else {
            $this->settings = Setting::create([
                "name" => "pos",
                "barcode" => false,
                "batch" => false,
                "expired_date" => false,
            ]);
        }

        if (\App\Models\People::where("type", "client")->count() == 0) {
            \App\Models\People::create(['name' => "نقدي", 'phone' => "", 'initialSalesBalance' => 0, 'initialPurchasesBalance' => 0, 'initialDepositsBalance' => 0, 'startingDate' => session("date"), 'type' => "client", 'blocked' => false, 'cash' => true]);
        }
        if (\App\Models\People::where("type", "client")->where("cash", true)->first() != null) {
            $this->currentClient = \App\Models\People::where("type", "client")->where("cash", true)->first()->toArray();
        } else {
            $this->currentClient = \App\Models\People::where("type", "client")->first()->toArray();
        }

        $this->currentSalesBalance = \App\Models\Sale::where("people_id", $this->currentClient['id'])->sum("remainder") + $this->currentClient['initialSalesBalance'];
        $this->banks = Bank::all();

        if ($this->banks->count() != 0) {
            $this->bank_id = $this->banks->first()->id;
        }
    }

    public function save()
    {
        if ($this->id == 0) {
            $sale = \App\Models\Sale::create([
                'people_id' => $this->currentClient['id'],
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'bank' => $this->bank,
                'paid' => floatval($this->paid),
                'remainder' => $this->remainder,
                'discount' => floatval($this->discount),
                'amount' => $this->amount,
                'note' => $this->note,
                'due_date' => $this->due_date,
                'user_id' => auth()->id(),
            ]);
            $this->id = $sale['id'];


            foreach ($this->cart as $item) {
                SaleDetail::create([
                    'sale_id' => $sale['id'],
                    'product_id' => $item['id'],
                    'quantity' => floatval($item['quantity']),
                    'price' => floatval($item['price']),
                ]);
            }

            foreach ($this->services as $service) {
                Service::create([
                    'sale_id' => $this->id,
                    'serviceName' => $service['serviceName'],
                    'amount' => floatval($service['serviceAmount'])
                ]);
            }

        } else {
            \App\Models\Sale::where("id", $this->id)->update([
                "people_id" => $this->currentClient['id'],
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'bank' => $this->bank,
                'paid' => floatval($this->paid),
                'remainder' => $this->remainder,
                'discount' => floatval($this->discount),
                'amount' => $this->amount,
                'note' => $this->note,
                'due_date' => $this->due_date,
                'user_id' => auth()->id(),
            ]);

            SaleDetail::where("sale_id", $this->id)->forceDelete();
            foreach ($this->cart as $item) {
                SaleDetail::create([
                    'sale_id' => $this->id,
                    'product_id' => floatval($item['product_id']),
                    'quantity' => floatval($item['quantity']),
                    'price' => floatval($item['price']),
                ]);
            }

            Service::where("sale_id", $this->id)->forceDelete();

            foreach ($this->services as $service) {
                Service::create([
                    'sale_id' => $this->id,
                    'serviceName' => $service['serviceName'],
                    'amount' => floatval($service['serviceAmount'])
                ]);
            }
        }
        $this->currentSalesBalance = People::find($this->currentClient['id'])->currentSalesBalance;

            $this->showInvoice($this->id);

        $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

        $this->resetData();

    }

    public function showInvoice($id = null)
    {
        $this->invoice['id'] = $id;
        $this->invoice['type'] = 'sale';
        $this->invoice['date'] = $this->due_date;
        $this->invoice['client'] = $this->currentClient['name'];
        $this->invoice['cart'] = $this->cart;
        $this->invoice['services'] = $this->services;
        $this->invoice['remainder'] = $this->remainder;
        $this->invoice['discount'] = floatval($this->discount);
        $this->invoice['paid'] = floatval($this->paid);
        $this->invoice['cost'] = floatval($this->cost);
        $this->invoice['amount'] = floatval($this->amount);
        $this->invoice['showMode'] = false;
        $this->dispatch('sale_created', $this->invoice);
    }

    public function chooseClient($client)
    {
        $this->currentClient = $client;
        $client = \App\Models\People::find($client['id']);
        $this->currentSalesBalance = $client->currentSalesBalance;


        if ($this->currentClient['cash']) {
            $this->paid = $this->amount;
        }
    }

    public function chooseProduct(\App\Models\Product $product)
    {
        if ($product->stock > 0) {
            $this->currentProduct = $product->toArray();
            $this->currentProduct['quantity'] = 1;
            $this->currentProduct['price'] = $product['sale_price'];
            $this->currentProduct['amount'] = $product['sale_price'];
            $this->currentProduct['stock'] = $product->stock;
            $this->productSearch = '';
        }

    }

    public function calcCurrentProduct()
    {
        $this->currentProduct['amount'] = floatval($this->currentProduct['price']) * floatval($this->currentProduct['quantity']);
    }

    public function addToCart()
    {
        $stock = $this->currentProduct['quantity'];
        $quantity = isset($this->cart[$this->currentProduct['id']]) ? $this->cart[$this->currentProduct['id']]['quantity'] : 0;
        if ($stock + $quantity > $this->currentProduct["stock"]) {
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
            if (!isset($this->cart[$this->currentProduct['id']])) {
                $this->cart[$this->currentProduct['id']] = $this->currentProduct;

                $this->cart[$this->currentProduct['id']]['amount'] = floatval($this->currentProduct['price']) * floatval($this->currentProduct['quantity']);
                $this->cart[$this->currentProduct['id']]['product_id'] = floatval($this->currentProduct['id']);

                $this->cost += $this->cart[$this->currentProduct['id']]['amount'];
                if ($this->currentClient['cash'] && $this->buyer == "client") {
                    $this->paid = $this->cost - $this->discount;
                }


            } else {
                $this->cost -= $this->cart[$this->currentProduct['id']]['amount'];
                $this->cart[$this->currentProduct['id']]['quantity'] += floatval($this->currentProduct['quantity']);
                $this->cart[$this->currentProduct['id']]['amount'] = floatval($this->cart[$this->currentProduct['id']]['price']) * floatval($this->cart[$this->currentProduct['id']]['quantity']);
                $this->cost += $this->cart[$this->currentProduct['id']]['amount'];

            }
            $this->currentProduct = [];
            $this->calcRemainder();

        }
    }

    public function addService()
    {
        $this->services[] = ['serviceName' => $this->serviceName, 'serviceAmount' => floatval($this->serviceAmount)];
        $this->totalServices += floatval($this->serviceAmount);
        $this->cost += floatval($this->serviceAmount);
        $this->calcRemainder();
        $this->reset("serviceName", "serviceAmount");
    }

    public function deleteFromCart($id)
    {
        $this->cost -= $this->cart[$id]['amount'];

        unset($this->cart[$id]);
        if (empty($this->cart) && empty($this->services)) {
            $this->amount = 0;
            $this->cost = 0;
            $this->remainder = 0;
            $this->paid = 0;
            $this->discount = 0;
        }
        $this->calcRemainder();

    }

    public function deleteService($key)
    {

        $this->cost -= floatval($this->services[$key]['serviceAmount']);
        unset($this->services[$key]);
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
        $this->invoice['buyer'] = $this->buyer;
        $this->invoice['clientType'] = 'العميل';
        $this->invoice['date'] = $sale['due_date'];
        $this->invoice['payment'] = $sale['payment'];
        $this->invoice['bank'] = $sale['bank'];
        $this->invoice['bank_id'] = $sale['bank_id'];
        $this->invoice['client'] = $this->currentClient['name'];
        $this->invoice['cart'] = SaleDetail::where('sale_id', $sale['id'])->join('products', 'products.id', '=', 'sale_details.product_id')->get()->keyBy("product_id")->toArray();
        $this->invoice['services'] = Service::where('sale_id', $sale['id'])->select("id", "serviceName", "amount As serviceAmount")->get()->toArray();
        $this->invoice['remainder'] = floatval($sale['remainder']);
        $this->invoice['paid'] = floatval($sale['paid']);
        $this->invoice['discount'] = floatval($sale['discount']);
        $this->invoice['cost'] = floatval($sale['amount']) + floatval($sale['discount']);
        $this->invoice['amount'] = $sale['amount'];
        $this->invoice['showMode'] = false;
        if ($this->invoice['paid'] > 0) {
            $this->payment = $sale['payment'];
            $this->invoice['paidId'] = $sale['id'];
        }

        $this->dispatch('sale_created', $this->invoice);
    }

    public function chooseSale($id)
    {
        $this->id = $this->invoice['id'];
        $this->payment = $this->invoice['payment'];
        $this->bank = $this->invoice['bank'];
        $this->bank_id = $this->invoice['bank_id'];
        $this->paid = floatval($this->invoice['paid']);
        $this->remainder = floatval($this->invoice['remainder']);
        $this->discount = floatval($this->invoice['discount']);
        $this->amount = floatval($this->invoice['amount']);
        $this->cost = floatval($this->invoice['amount']) + floatval($this->invoice['discount']);
        $this->due_date = $this->invoice['date'];
        $this->cart = $this->invoice['cart'];
        $this->services = $this->invoice['services'];
        foreach ($this->cart as $item) {
            $this->cart[$item['product_id']]['amount'] = floatval($item['price']) * floatval($item['quantity']);
        }
        $this->editMode = false;
    }

    public function editMessage()
    {
        $this->confirm("  هل توافق على تعديل الفاتورة ؟", [
            'inputAttributes' => [],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "save",
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
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

        \App\Models\Sale::where('id', $id)->forceDelete();

        $this->currentSalesBalance = People::find($this->currentClient['id'])->currentSalesBalance;
        $this->alert('success', 'تم الإلغاء بنجاح', ['timerProgressBar' => true]);

    }

    public function calcRemainder()
    {
        $this->amount = $this->cost - floatval($this->discount);
        if ($this->currentClient['cash'] && $this->buyer == "client") {
            $this->paid = $this->amount;
            $this->remainder = 0;
        } else {
            $this->remainder = floatval($this->amount) - floatval($this->paid);
        }
    }

    public function resetData($item = null)
    {
        $item == "currentClient" ? $this->reset('search', 'clientSearch', 'id', 'oldQuantities', $item) : $this->reset('currentProduct', 'cart', 'bank', 'payment', 'bank', 'bank_id', 'search', 'clientSearch', 'paid', 'remainder', 'amount', 'cost', 'discount', 'id', 'services', 'serviceAmount', 'serviceName', 'totalServices', 'oldQuantities', $item);
    }

    public function render()
    {

        if ($this->payment == "bank" && $this->bank_id == null) {
            if ($this->banks->count() != 0) {
                $this->bank_id = $this->banks->first()->id;
            }
        }

        if (!empty($this->currentClient)) {
            $this->sales = \App\Models\Sale::where('people_id', $this->currentClient['id'])
                ->where('id', 'LIKE', '%' . $this->saleSearch . '%')->latest()->get();
        }
        if ($this->due_date == '') {
            $this->due_date = session("date");
        }
        $this->clients = \App\Models\People::where("type", $this->buyer)->where('name', 'LIKE', '%' . $this->clientSearch . '%')->get();


        if ($this->settings->barcode) {
            $barcode = \App\Models\Product::where("barcode", $this->productSearch)->first();

            if ($barcode) {
                $this->chooseProduct($barcode);
            }
        }

        $product = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->simplePaginate(10);

        return view('livewire.sale', [
            'products' => $product
        ]);
    }
}
