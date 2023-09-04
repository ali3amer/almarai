<?php

namespace App\Livewire;

use App\Models\SaleDetail;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Sale extends Component
{

    public string $title = 'المبيعات';
    public int $id = 0;
    public string $modalName = '';

    public string $search = '';
    public Collection $sales;
    public Collection $clients;
    public Collection $products;
    public string $productSearch = '';
    public string $clientSearch = '';

    public float $total_amount = 0;
    public $discount = 0;
    public float $paid = 0;

    public array $currentClient = [];
    public array $oldQuantities = [];
    public array $currentProduct = [];
    public array $cart = [];
    public string $saleSearch = '';

    public function save()
    {
        if ($this->id == 0) {
            $sale = \App\Models\Sale::create([
                'client_id' => $this->currentClient['id'],
                'paid' => $this->paid,
                'discount' => $this->discount,
                'total_amount' => $this->total_amount,
                'sale_date' => now(),
            ]);

            foreach ($this->cart as $item) {
                SaleDetail::create([
                    'sale_id' => $sale['id'],
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['sale_price'],
                ]);
                \App\Models\Product::where('id', $item['id'])->decrement('stock', $item['quantity']);
            }
            session()->flash('success', 'تم الحفظ بنجاح');

        } else {
            \App\Models\Sale::where('id', $this->id)->update([
                'paid' => $this->paid,
                'discount' => $this->discount,
                'total_amount' => $this->total_amount,
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
        $this->paid = $this->total_amount - $this->discount;
        $this->currentProduct = [];
    }

    public function deleteFromCart($id)
    {
        $this->total_amount -= $this->cart[$id]['amount'];
        $this->paid = $this->total_amount - $this->discount;
        unset($this->cart[$id]);
    }

    public function calcDiscount()
    {
        $this->paid = $this->total_amount - floatval($this->discount);
    }

    public function getSales()
    {
//        $this->sales = \App\Models\Sale::where('client_id', $this->currentClient['id'])->with('saleDetails.product')->get();
    }

    public function chooseSale($sale)
    {
        $this->total_amount = $sale['total_amount'];
        $this->discount = $sale['discount'];
        $this->paid = $sale['paid'];
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
    public function resetData()
    {
        $this->reset('currentClient', 'currentProduct', 'cart', 'search', 'clientSearch', 'discount', 'paid', 'total_amount', 'id', 'oldQuantities');
    }

    public function render()
    {
        if (!empty($this->currentClient)) {
            $this->sales = \App\Models\Sale::where('client_id', $this->currentClient['id'])
                ->where('id', 'LIKE', '%' . $this->saleSearch . '%')->orWhere('sale_date', 'LIKE', '%' . $this->saleSearch . '%')
                ->with('saleDetails.product')->get();
        }
        $this->clients = \App\Models\Client::where('clientName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->get();
        return view('livewire.sale');
    }
}
