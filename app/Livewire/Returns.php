<?php

namespace App\Livewire;

use App\Models\SaleDetail;
use App\Models\SaleReturn;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Returns extends Component
{

    public string $title = 'المرتجعات';

    public string $productName = '';
    public int $id = 0;
    public float $price = 0;
    public float $quantity = 0;
    public float $amount = 0;
    public float $quantityReturn = 0;
    public float $priceReturn = 0;

    public string $clientSearch = '';
    public Collection $clients;
    public Collection $saleDetails;
    public Collection $sales;
    public array $currentClient = [];
    public array $currentProduct = [];
    public string $saleSearch = '';
    public  array $currentSale = [];

    public function chooseClient($client)
    {
        $this->currentClient = [];
        $this->currentClient = $client;
    }

    public function chooseSale($sale)
    {
        $this->currentSale = $sale;
        $this->id = $this->currentSale['id'];
        $this->saleDetails = SaleDetail::with('product')->where('sale_id', $this->currentSale['id'])->get();

    }

    public function chooseProduct($detail, $product)
    {
        $this->currentProduct = $detail;
        $this->productName = $product['productName'];
        $this->quantity = $detail['quantity'];
        $this->price = $detail['price'];
        $this->amount = $detail['quantity'] * $detail['price'];
    }

    public function calcQuantity()
    {
        $this->quantity = $this->currentProduct['quantity'] - $this->quantityReturn;
        $this->amount = $this->quantity * $this->price;
        $this->priceReturn = $this->currentProduct['price'] * $this->quantityReturn;
    }

    public function save()
    {
        if ($this->amount == 0) {
            SaleDetail::where('id', $this->currentProduct['id'])->delete();
        } else {
            SaleDetail::where('id', $this->currentProduct['id'])->decrement('quantity', $this->quantityReturn);
        }

        \App\Models\Product::where('id', $this->currentProduct['product_id'])->increment('stock', $this->quantityReturn);

        \App\Models\Sale::where('id', $this->currentProduct['sale_id'])->decrement('total_amount', $this->priceReturn);

        SaleReturn::create([
            'sale_id' => $this->currentProduct['sale_id'],
            'product_id' => $this->currentProduct['product_id'],
            'quantity' => $this->quantityReturn,
            'price' => $this->priceReturn
        ]);

        session()->flash('success', 'تم تعديل الفاتوره بنجاح');
        $this->resetData();
    }

    public function resetData()
    {
        $this->reset('productName', 'amount', 'quantity', 'price', 'quantityReturn', 'clientSearch', 'currentClient', 'currentProduct', 'currentSale', 'saleSearch',);
    }

    public function render()
    {
        $this->clients = \App\Models\Client::where('clientName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        if (!empty($this->currentClient)) {
            $this->sales = \App\Models\Sale::where('client_id', $this->currentClient['id'])->where('id', 'LIKE', '%' . $this->saleSearch . '%')->get();
        }
        return view('livewire.returns');
    }
}
