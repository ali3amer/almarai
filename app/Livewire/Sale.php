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
        $this->currentBalance = $this->currentClient['currentBalance'];
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

            if ($this->buyer == 'client') {
                $this->currentClient['currentBalance'] += $this->total_amount;
            } elseif ($this->buyer == 'employee') {
                $this->currentClient['currentBalance'] = 0;
            } elseif ($this->buyer == 'supplier') {
                $this->currentClient['currentBalance'] -= $this->total_amount;
            }

            SaleDebt::create([
                'sale_id' => $sale['id'],
                'paid' => 0,
                'bank' => '',
                'payment' => 'cash',
                'remainder' => $this->total_amount,
                'client_balance' => $this->currentClient['currentBalance'],
                'due_date' => $this->sale_date,
                'user_id' => auth()->id()
            ]);

            if ($this->buyer == 'client') {
                \App\Models\Client::where('id', $this->currentClient['id'])->increment('currentBalance', $this->total_amount);
            } elseif ($this->buyer == 'supplier') {
                \App\Models\Supplier::where('id', $this->currentClient['id'])->decrement('currentBalance', $this->total_amount);
            }

            if ($this->paid != 0) {

                if ($this->buyer == 'client') {
                    $this->currentClient['currentBalance'] -= $this->paid;
                }

                SaleDebt::create([
                    'sale_id' => $sale['id'],
                    'paid' => $this->paid,
                    'bank' => $this->bank,
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                    'remainder' => $this->remainder,
                    'client_balance' => $this->currentClient['currentBalance'],
                    'due_date' => $this->sale_date,
                    'user_id' => auth()->id()
                ]);

                if ($this->payment == 'cash') {
                    \App\Models\Safe::first()->increment('currentBalance', $this->paid);
                } else {
                    Bank::where('id', $this->bank_id)->increment('currentBalance', $this->paid);
                }

                if ($this->buyer == 'client') {
                    \App\Models\Client::where('id', $this->currentClient['id'])->decrement('currentBalance', $this->paid);
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

            $this->id = $sale['id'];

            $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

        } else {
            $sale = \App\Models\Sale::where('id', $this->id)->first();
            if ($this->buyer == 'client') {
                $this->currentClient['currentBalance'] -= $sale['total_amount'];
                \App\Models\Client::where('id', $this->currentClient['id'])->decrement('currentBalance', $sale['total_amount']);
            } elseif ($this->buyer == 'supplier') {
                $this->currentClient['currentBalance'] += $sale['total_amount'];
                \App\Models\Supplier::where('id', $this->currentClient['id'])->increment('currentBalance', $sale['total_amount']);
            }

            \App\Models\Sale::where('id', $this->id)->update([
                'total_amount' => $this->total_amount,
                'sale_date' => $this->sale_date,
                'user_id' => auth()->id()
            ]);

            if ($this->buyer == 'client') {
                \App\Models\Client::where('id', $this->currentClient['id'])->increment('currentBalance', $this->total_amount);
                $this->currentClient['currentBalance'] += $this->total_amount;
            } elseif ($this->buyer == 'supplier') {
                \App\Models\Supplier::where('id', $this->currentClient['id'])->decrement('currentBalance', $this->total_amount);
                $this->currentClient['currentBalance'] -= $this->total_amount;
            }

            $debt = SaleDebt::where('sale_id', $this->id)->first();

            if ($debt['payment'] == 'cash') {
                \App\Models\Safe::first()->decrement('currentBalance', $debt['paid']);
            } else {
                Bank::where('id', $debt['bank_id'])->decrement('currentBalance', $debt['paid']);
            }

            $debt->update([
                'sale_id' => $this->id,
                'paid' => $this->paid,
                'bank' => $this->bank,
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'remainder' => $this->remainder,
                'client_balance' => $this->currentClient['currentBalance'],
                'due_date' => $this->sale_date,
                'user_id' => auth()->id()
            ]);

            if ($this->payment == 'cash') {
                \App\Models\Safe::first()->increment('currentBalance', $this->paid);
            } else {
                Bank::where('id', $this->bank_id)->increment('currentBalance', $this->paid);
            }

            SaleDetail::where('sale_id', $this->id)->delete();

            foreach ($this->oldQuantities as $key => $quantity) {
                \App\Models\Product::where('id', $key)->increment('stock', $quantity);
            }

            foreach ($this->cart as $item) {
                SaleDetail::create([
                    'sale_id' => $this->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['sale_price'],
                ]);
                \App\Models\Product::where('id', $item['id'])->decrement('stock', $item['quantity']);
            }
            $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);


        }
        $this->invoice['id'] = $sale['id'];
        $this->invoice['type'] = 'sale';
        $this->invoice['sale_date'] = $sale['sale_date'];
        $this->invoice['client'] = $this->currentClient[$this->buyer . 'Name'];
        $this->invoice['cart'] = $this->cart;
        $this->invoice['remainder'] = $this->remainder;
        $this->invoice['paid'] = $this->paid;
        $this->invoice['total_amount'] = $this->total_amount;
        $this->dispatch('sale_created', $this->invoice);
        $this->resetData();

    }

    public function chooseClient($client)
    {
        $this->currentClient = [];
        $this->currentClient = $client;
        $this->currentBalance = $this->currentClient['currentBalance'];
    }

    public function chooseProduct($product)
    {
        $this->currentProduct = $product;
        $this->currentProduct['quantity'] = 1;
        $this->currentProduct['amount'] = $product['sale_price'];
        $this->productSearch = '';
        $this->dispatch('sale_price_focus');

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
            $this->currentBalance -= $this->remainder;
            $this->paid = 0;
        }
    }

    public function showSales()
    {
        $this->editMode = !$this->editMode;
    }

    public function chooseSale($sale)
    {
        $this->editMode = !$this->editMode;
        $this->total_amount = $sale['total_amount'];
        $this->paid = $sale['sale_debts'][0]['paid'];
        $this->payment = $sale['sale_debts'][0]['payment'];
        $this->bank = $sale['sale_debts'][0]['bank'];
        $this->sale_date = $sale['sale_date'];
        $this->id = $sale['id'];
        foreach ($sale['sale_details'] as $detail) {
            $this->cart[$detail['product_id']] = [
                'id' => $detail['product_id'],
                'sale_id' => $detail['sale_id'],
                'product_id' => $detail['product_id'],
                'productName' => $detail['product']['productName'],
                'quantity' => $detail['quantity'],
                'sale_price' => $detail['price'],
                'amount' => $detail['price'] * $detail['quantity'],
            ];

            $this->oldQuantities[$detail['product_id']] = $detail['quantity'];
        }

    }

    public function calcRemainder()
    {
        $this->currentBalance -= $this->remainder;
        $this->remainder = $this->total_amount - floatval($this->paid);
        $this->currentBalance += $this->remainder;
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
                    ->where('id', 'LIKE', '%' . $this->saleSearch . '%')->where('sale_date', 'LIKE', '%' . $this->saleSearch . '%')
                    ->with('saleDetails.product', 'saleDebts')->get();
            } elseif ($this->buyer == 'employee') {
                $this->sales = \App\Models\Sale::where('employee_id', $this->currentClient['id'])
                    ->where('id', 'LIKE', '%' . $this->saleSearch . '%')->where('sale_date', 'LIKE', '%' . $this->saleSearch . '%')
                    ->with('saleDetails.product', 'saleDebts')->get();
            } elseif ($this->buyer == 'supplier') {
                $this->sales = \App\Models\Sale::where('supplier_id', $this->currentClient['id'])
                    ->where('id', 'LIKE', '%' . $this->saleSearch . '%')->where('sale_date', 'LIKE', '%' . $this->saleSearch . '%')
                    ->with('saleDetails.product', 'saleDebts')->get();
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
