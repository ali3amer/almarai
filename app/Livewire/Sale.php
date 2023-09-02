<?php

namespace App\Livewire;

use App\Models\SaleDetail;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Sale extends Component
{

    public string $title = 'المبيعات';

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
    public array $currentProduct = [];
    public array $cart = [];

    public function save()
    {
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

            session()->flash('success', 'تم الحفظ بنجاح');

            $this->resetData();
        }
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

    public function resetData()
    {
        $this->reset('currentClient', 'currentProduct', 'cart', 'search', 'clientSearch', 'discount', 'paid', 'total_amount');
    }

    public function render()
    {
        $this->clients = \App\Models\Client::where('clientName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->get();
        $this->sales = \App\Models\Sale::all();
        return view('livewire.sale');
    }
}
