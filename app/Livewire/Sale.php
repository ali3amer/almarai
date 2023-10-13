<?php

namespace App\Livewire;

use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Bank;
use App\Models\SaleDebt;
use App\Models\SaleDetail;
use Cassandra\Date;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Sale extends Component
{
    use LivewireAlert;

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
    public Collection $products;
    public Collection $banks;
    public string $productSearch = '';
    public string $clientSearch = '';

    public float $total_amount = 0;
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

    public function mount()
    {
        $this->currentClient = \App\Models\Client::find(1)->toArray();
        $this->banks = Bank::all();
    }

    public function save()
    {
        if ($this->id == 0) {
            $sale = \App\Models\Sale::create([
                'client_id' => $this->buyer == 'client' ? $this->currentClient['id'] : null,
                'employee_id' => $this->buyer == 'employee' ? $this->currentClient['id'] : null,
                'supplier_id' => $this->buyer == 'supplier' ? $this->currentClient['id'] : null,
                'total_amount' => $this->total_amount,
                'sale_date' => $this->sale_date,
                'user_id' => auth()->id(),
            ]);

            $type = floatval($this->paid) == 0 ? 'debt' : 'pay';

            if ($this->buyer == 'client') {
                if (floatval($this->paid) == 0) {
                    \App\Models\ClientDebt::create([
                        'client_id' => $this->currentClient['id'],
                        'paid' => 0,
                        'debt' => $this->remainder,
                        'type' => $type,
                        'bank' => $this->bank,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'due_date' => $this->sale_date,
                        'note' => 'تم شراء بالآجل',
                        'user_id' => auth()->id()
                    ]);
                } else {
                    \App\Models\ClientDebt::create([
                        'client_id' => $this->currentClient['id'],
                        'paid' => floatval($this->paid),
                        'debt' => 0,
                        'type' => $type,
                        'bank' => $this->bank,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'due_date' => $this->sale_date,
                        'note' => 'تم إستلام مبلغ',
                        'user_id' => auth()->id()
                    ]);
                }

            } elseif ($this->buyer == 'employee') {
                if (floatval($this->paid) == 0) {
                    \App\Models\EmployeeDebt::create([
                        'employee_id' => $this->currentClient['id'],
                        'paid' => 0,
                        'debt' => $this->remainder,
                        'type' => $type,
                        'bank' => $this->bank,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'due_date' => $this->sale_date,
                        'note' => 'تم شراء بالآجل',
                        'user_id' => auth()->id()
                    ]);
                } else {
                    \App\Models\EmployeeDebt::create([
                        'employee_id' => $this->currentClient['id'],
                        'paid' => floatval($this->paid),
                        'debt' => 0,
                        'type' => $type,
                        'bank' => $this->bank,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'due_date' => $this->sale_date,
                        'note' => 'تم إستلام مبلغ',
                        'user_id' => auth()->id()
                    ]);
                }


            } elseif ($this->buyer == 'supplier') {
                if (floatval($this->paid) == 0) {
                    \App\Models\SupplierDebt::create([
                        'supplier_id' => $this->currentClient['id'],
                        'paid' => 0,
                        'debt' => $this->remainder,
                        'type' => $type,
                        'bank' => $this->bank,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'due_date' => $this->sale_date,
                        'note' => 'تم شراء بالآجل',
                        'user_id' => auth()->id()
                    ]);
                } else {
                    \App\Models\SupplierDebt::create([
                        'supplier_id' => $this->currentClient['id'],
                        'paid' => floatval($this->paid),
                        'debt' => 0,
                        'type' => $type,
                        'bank' => $this->bank,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'due_date' => $this->sale_date,
                        'note' => 'تم إستلام مبلغ',
                        'user_id' => auth()->id()
                    ]);
                }

            }


        }

        foreach ($this->cart as $item) {
            SaleDetail::create([
                'sale_id' => $sale['id'],
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['sale_price'],
            ]);

            \App\Models\Product::where('id', $item['id'])->decrement('stock', $item['quantity']);
        }


        $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

        $this->invoice['id'] = $sale['id'];
        $this->invoice['type'] = 'sale';
        $this->invoice['sale_date'] = $sale['sale_date'];
        $this->invoice['client'] = $this->currentClient[$this->buyer . 'Name'];
        $this->invoice['cart'] = $this->cart;
        $this->invoice['remainder'] = $this->remainder;
        $this->invoice['paid'] = $this->paid;
        $this->invoice['total_amount'] = $this->total_amount;
        $this->invoice['showMode'] = false;
        $this->dispatch('sale_created', $this->invoice);
        $this->resetData();

    }

    public function chooseClient($client)
    {
        $this->currentClient = $client;
        $this->currentClient['blocked'] = $this->buyer != 'employee' ? $this->currentClient['blocked'] : false;
    }

    public function chooseProduct($product)
    {
        $this->currentProduct = $product;
        $this->currentProduct['quantity'] = 1;
        $this->currentProduct['amount'] = $product['sale_price'];
        $this->productSearch = '';

    }

    public function calcCurrentProduct()
    {
        $this->currentProduct['amount'] = floatval($this->currentProduct['sale_price']) * floatval($this->currentProduct['quantity']);
    }


    public function addToCart()
    {
        $this->cart[$this->currentProduct['id']] = $this->currentProduct;
        $this->cart[$this->currentProduct['id']]['amount'] = floatval($this->currentProduct['sale_price']) * floatval($this->currentProduct['quantity']);
        $this->total_amount += $this->cart[$this->currentProduct['id']]['amount'];
        $this->paid = $this->total_amount;
        $this->currentProduct = [];
        $this->calcRemainder();
        $this->dispatch('productSearchFocus');
    }

    public function deleteFromCart($id)
    {
        $this->total_amount -= $this->cart[$id]['amount'];
        $this->paid = $this->total_amount;

        $this->calcRemainder();
        unset($this->cart[$id]);
        if (empty($this->cart)) {
            $this->remainder = 0;
            $this->paid = 0;
        }
    }

    public function showSales()
    {
        $this->editMode = !$this->editMode;
    }

    public function getSale($sale)
    {
        $this->invoice['id'] = $sale['id'];
        $this->invoice['type'] = 'sale';
        $this->invoice['sale_date'] = $sale['sale_date'];
        $this->invoice['client'] = $this->currentClient[$this->buyer . 'Name'];
        $this->invoice['cart'] = SaleDetail::where('sale_id', $sale['id'])->join('products', 'products.id', '=', 'sale_details.product_id')->get()->toArray();
        $this->invoice['remainder'] = $this->remainder;
        $this->invoice['paid'] = $this->paid;
        $this->invoice['total_amount'] = $sale['total_amount'];
        $this->invoice['showMode'] = true;
        $this->dispatch('sale_created', $this->invoice);
    }
    public function cancelSale($sale)
    {
        \App\Models\Sale::where('id', $sale['id'])->delete();
        $items = SaleDetail::where('sale_id', $sale['id'])->get();
        foreach ($items as $item) {
            \App\Models\Product::where('id', $item['product_id'])->increment('stock', $item['quantity']);
            \App\Models\SaleDetail::where('id', $item['id'])->delete();
        }

        \App\Models\ClientDebt::create([
            'client_id' => $this->currentClient['id'],
            'paid' => 0,
            'debt' => $sale['total_amount'],
            'type' => 'debt',
            'bank' => '',
            'payment' => 'cash',
            'bank_id' => null,
            'due_date' => $this->sale_date,
            'note' => $sale['id'] . '#تم إلغاء الفاتوره رقم ',
            'user_id' => auth()->id()
        ]);

    }

    public function calcRemainder()
    {
        $this->remainder = $this->total_amount - floatval($this->paid);
    }

    public function resetData($item = null)
    {
        $this->reset('currentProduct', 'cart', 'search', 'clientSearch', 'paid', 'remainder', 'total_amount', 'id', 'oldQuantities', $item);
    }

    public function render()
    {


        if (!empty($this->currentClient)) {
            if ($this->buyer == 'client') {
                $this->sales = \App\Models\Sale::where('client_id', $this->currentClient['id'])
                    ->where('id', 'LIKE', '%' . $this->saleSearch . '%')->where('sale_date', 'LIKE', '%' . $this->saleSearch . '%')->latest()->get();
            } elseif ($this->buyer == 'employee') {
                $this->sales = \App\Models\Sale::where('employee_id', $this->currentClient['id'])
                    ->where('id', 'LIKE', '%' . $this->saleSearch . '%')->where('sale_date', 'LIKE', '%' . $this->saleSearch . '%')->latest()->get();
            } elseif ($this->buyer == 'supplier') {
                $this->sales = \App\Models\Sale::where('supplier_id', $this->currentClient['id'])
                    ->where('id', 'LIKE', '%' . $this->saleSearch . '%')->where('sale_date', 'LIKE', '%' . $this->saleSearch . '%')->latest()->get();
            }
        }
        if ($this->sale_date == '') {
            $this->sale_date = date('Y-m-d');
        }
        if ($this->buyer == 'client') {
            $this->clients = \App\Models\Client::where('clientName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        } elseif ($this->buyer == 'employee') {
            $this->clients = \App\Models\Employee::where('employeeName', 'LIKE', '%' . $this->clientSearch . '%')->get();

        } elseif ($this->buyer == 'supplier') {
            $this->clients = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        }
        $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->get();
        return view('livewire.sale');
    }
}
