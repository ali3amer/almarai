<?php

namespace App\Livewire;

use App\Models\SaleDetail;
use App\Models\SaleReturn;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use mysql_xdevapi\CollectionRemove;

class Returns extends Component
{

    public string $title = 'المرتجعات';

    public string $productName = '';
    public bool $editMode = false;
    public int $id = 0;
    public float $price = 0;
    public float $quantity = 0;
    public float $amount = 0;
    public float $quantityReturn = 0;
    public float $priceReturn = 0;

    public string $clientSearch = '';
    public Collection $clients;
    public Collection $returns;
    public Collection $saleDetails;
    public Collection $sales;
    public array $currentClient = [];
    public array $currentDetail = [];
    public string $saleSearch = '';
    public array $currentSale = [];

    public function chooseClient($client)
    {
        $this->currentClient = [];
        $this->currentClient = $client;
    }

    public function chooseSale($sale, $editMode)
    {
        $this->editMode = $editMode;

        $this->currentSale = [];
        $this->currentSale = $sale;
        $this->id = $this->currentSale['id'];
        $this->saleDetails = SaleDetail::with('product')->where('sale_id', $this->currentSale['id'])->get();

    }

    public function chooseDetail($detail, $product)
    {
        $this->id = $this->editMode ? $detail['id'] : 0;
        $this->currentDetail = $detail;
        $this->productName = $product['productName'];
        $this->quantity = $detail['quantity'];
        $this->price = $detail['price'];
        $this->amount = $detail['quantity'] * $detail['price'];
    }

    public function getReturns($sale)
    {
        $this->chooseSale($sale, true);
        $this->returns = SaleReturn::with('product')->where('sale_id', $sale['id'])->get();
    }

    public function calcQuantity()
    {
        $this->quantity = $this->currentDetail['quantity'] - $this->quantityReturn;
        $this->amount = $this->quantity * $this->price;
        $this->priceReturn = $this->currentDetail['price'] * $this->quantityReturn;
    }

    public function save()
    {
        if ($this->id == 0) {

            SaleDetail::where('id', $this->currentDetail['id'])->decrement('quantity', $this->quantityReturn);

            \App\Models\Product::where('id', $this->currentDetail['product_id'])->increment('stock', $this->quantityReturn);

            \App\Models\Sale::where('id', $this->currentDetail['sale_id'])->decrement('total_amount', $this->priceReturn);

            SaleReturn::create([
                'sale_id' => $this->currentDetail['sale_id'],
                'product_id' => $this->currentDetail['product_id'],
                'quantity' => $this->quantityReturn,
                'price' => $this->currentDetail['price']
            ]);
        } else {
            SaleDetail::where('sale_id', $this->currentDetail['sale_id'])->where('product_id', $this->currentDetail['product_id'])->increment('quantity', $this->quantityReturn);

            \App\Models\Product::where('id', $this->currentDetail['product_id'])->decrement('stock', $this->quantityReturn);

            \App\Models\Sale::where('id', $this->currentDetail['sale_id'])->increment('total_amount', $this->priceReturn);

            SaleReturn::where('id', $this->id)->update([
                'quantity' => $this->currentDetail['quantity'] - floatval($this->quantityReturn),
            ]);
        }

        session()->flash('success', 'تم تعديل الفاتوره بنجاح');
        $this->resetData();
    }

    public function delete($return)
    {
        SaleReturn::where('id', $return['id'])->delete();
        \App\Models\Sale::where('id', $return['sale_id'])->increment('total_amount', $return['quantity'] * $return['price']);
        SaleDetail::where('sale_id', $return['sale_id'])->where('product_id', $return['product_id'])->increment('quantity', $return['quantity']);
        \App\Models\Product::where('id', $return['product_id'])->decrement('stock', $return['quantity']);
    }

    public function resetData()
    {
        $this->reset('productName', 'editMode', 'amount', 'quantity', 'price', 'quantityReturn', 'clientSearch', 'currentClient', 'currentDetail', 'currentSale', 'saleSearch',);
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
