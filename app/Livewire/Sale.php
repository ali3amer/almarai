<?php

namespace App\Livewire;

use App\Models\SaleDebt;
use App\Models\SaleDetail;
use Cassandra\Date;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Sale extends Component
{

    public string $title = 'المبيعات';
    public int $id = 0;
    public int $debtId = 0;
    public string $sale_date = '';
    public string $due_date = '';
    public bool $print = false;

    public string $search = '';
    public Collection $sales;
    public Collection $clients;
    public Collection $products;
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
    public bool $payMode = false;
    public array $currentSaleDebts = [];
    public array $currentSale = [];
    /**
     * @var \Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public Collection $saleDebts;

    public function save()
    {
        if ($this->id == 0) {
            $sale = \App\Models\Sale::create([
                'client_id' => $this->currentClient['id'],
                'total_amount' => $this->total_amount,
                'sale_date' => $this->sale_date,
            ]);

            $this->currentClient['currentBalance'] += $this->total_amount;

            SaleDebt::create([
                'sale_id' => $sale['id'],
                'paid' => 0,
                'bank' => '',
                'payment' => 'cash',
                'remainder' => $this->total_amount,
                'client_balance' => $this->currentClient['currentBalance'],
                'due_date' => $this->sale_date
            ]);

            \App\Models\Client::where('id', $this->currentClient['id'])->increment('currentBalance', $this->total_amount);

            if ($this->paid != 0) {
                $this->currentClient['currentBalance'] -= $this->paid;
                SaleDebt::create([
                    'sale_id' => $sale['id'],
                    'paid' => $this->paid,
                    'bank' => $this->bank,
                    'payment' => $this->payment,
                    'remainder' => $this->remainder,
                    'client_balance' => $this->currentClient['currentBalance'],
                    'due_date' => $this->sale_date
                ]);


                \App\Models\Client::where('id', $this->currentClient['id'])->decrement('currentBalance', $this->paid);
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

            session()->flash('success', 'تم الحفظ بنجاح');

        } else {
            $sale = \App\Models\Sale::where('id', $this->id)->first();
            $this->currentClient['currentBalance'] -= $sale['total_amount'];
            \App\Models\Client::where('id', $this->currentClient['id'])->decrement('currentBalance', $sale['total_amount']);
            \App\Models\Sale::where('id', $this->id)->update([
                'total_amount' => $this->total_amount,
                'sale_date' => $this->sale_date
            ]);
            \App\Models\Client::where('id', $this->currentClient['id'])->increment('currentBalance', $this->total_amount);
            $this->currentClient['currentBalance'] += $this->total_amount;

            SaleDebt::where('sale_id', $this->id)->first()->update([
                'sale_id' => $this->id,
                'paid' => $this->paid,
                'bank' => $this->bank,
                'payment' => $this->payment,
                'remainder' => $this->remainder,
                'current_balance' => $this->currentClient['currentBalance'],
                'due_date' => $this->sale_date
            ]);

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
            session()->flash('success', 'تم التعديل بنجاح');


        }
        $this->resetData();

    }

    public function printInvoice($print)
    {
    }

    public function edit($sale)
    {

    }

    public function delete($id)
    {

    }

    public function chooseClient($client)
    {
        $this->currentClient = [];
        $this->currentClient = $client;
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
        $this->cart[$this->currentProduct['id']]['amount'] = $this->currentProduct['sale_price'] * $this->currentProduct['quantity'];
        $this->total_amount += $this->cart[$this->currentProduct['id']]['amount'];
        $this->currentProduct = [];
    }

    public function deleteFromCart($id)
    {
        $this->total_amount -= $this->cart[$id]['amount'];
        $this->calcRemainder();
        unset($this->cart[$id]);
        if (empty($this->cart)) {
            $this->remainder = 0;
            $this->paid = 0;
        }
    }

    public function chooseSale($sale)
    {
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
        $this->remainder = $this->total_amount - floatval($this->paid);
    }

    public function resetData()
    {
        $this->reset('currentClient', 'currentProduct', 'cart', 'search', 'clientSearch', 'paid', 'remainder', 'total_amount', 'id', 'oldQuantities');
    }

    public function render()
    {

        if (!empty($this->currentClient)) {

            $this->sales = \App\Models\Sale::where('client_id', $this->currentClient['id'])
                ->where('id', 'LIKE', '%' . $this->saleSearch . '%')->orWhere('sale_date', 'LIKE', '%' . $this->saleSearch . '%')
                ->with('saleDetails.product', 'saleDebts')->get();
        }  else {
            $this->sale_date = date('Y-m-d');
        }
        $this->clients = \App\Models\Client::where('clientName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->get();
        return view('livewire.sale');
    }
}
