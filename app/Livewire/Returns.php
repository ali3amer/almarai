<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\SaleDebt;
use App\Models\SaleDetail;
use App\Models\SaleReturn;
use Illuminate\Database\Eloquent\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use mysql_xdevapi\CollectionRemove;

class Returns extends Component
{
    use LivewireAlert;
    public string $title = 'المرتجعات';

    public string $productName = '';
    public bool $editMode = false;
    public int $id = 0;
    public float $price = 0;
    public float $quantity = 0;
    public float $amount = 0;
    public float $quantityReturn = 0;
    public float $priceReturn = 0;

    public string $return_date = '';
    public string $clientSearch = '';
    public Collection $clients;
    public Collection $returns;
    public Collection $saleDetails;
    public Collection $sales;
    public array $currentClient = [];
    public array $currentDetail = [];
    public string $saleSearch = '';
    public array $currentSale = [];
    public string $buyer = 'client';

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

        $this->return_date = $detail['return_date'] ?? $this->return_date;
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
        $debt = SaleDebt::where('id', $this->currentDetail['sale_id'])->where('paid', '!=', 0)->first();

        if ($this->id == 0) {

            SaleDetail::where('id', $this->currentDetail['id'])->decrement('quantity', $this->quantityReturn);

            \App\Models\Product::where('id', $this->currentDetail['product_id'])->increment('stock', $this->quantityReturn);

            \App\Models\Sale::where('id', $this->currentDetail['sale_id'])->decrement('total_amount', $this->priceReturn);


            if (isset($debt['paid'])) {
                if ($debt['payment'] == 'cash') {
                    \App\Models\Safe::first()->decrement('currentBalance', $debt['paid']);
                } else {
                    Bank::where('id', $debt['bank_id'])->decrement('currentBalance', $debt['paid']);
                }
                \App\Models\Client::where('id', $this->currentClient['id'])->decrement('currentBalance', $debt['paid']);
            }

            SaleReturn::create([
                'sale_id' => $this->currentDetail['sale_id'],
                'product_id' => $this->currentDetail['product_id'],
                'quantity' => $this->quantityReturn,
                'return_date' => $this->return_date,
                'price' => $this->currentDetail['price']
            ]);
            $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
        } else {
            SaleDetail::where('sale_id', $this->currentDetail['sale_id'])->where('product_id', $this->currentDetail['product_id'])->increment('quantity', $this->quantityReturn);

            \App\Models\Product::where('id', $this->currentDetail['product_id'])->decrement('stock', $this->quantityReturn);

            \App\Models\Sale::where('id', $this->currentDetail['sale_id'])->increment('total_amount', $this->priceReturn);


            if (isset($debt['paid'])) {
                if ($debt['payment'] == 'cash') {
                    \App\Models\Safe::first()->decrement('currentBalance', $debt['paid']);
                } else {
                    Bank::where('id', $debt['bank_id'])->decrement('currentBalance', $debt['paid']);
                }
                \App\Models\Client::where('id', $this->currentClient['id'])->decrement('currentBalance', $debt['paid']);
            }

            SaleReturn::where('id', $this->id)->update([
                'quantity' => $this->currentDetail['quantity'] - floatval($this->quantityReturn),
                'return_date' => $this->return_date
            ]);
        }

        $this->alert('success', 'تم تعديل الفاتوره بنجاح', ['timerProgressBar' => true]);
        $this->resetData();
    }

    public function delete($return)
    {
        SaleReturn::where('id', $return['id'])->delete();
        \App\Models\Sale::where('id', $return['sale_id'])->increment('total_amount', $return['quantity'] * $return['price']);
        SaleDetail::where('sale_id', $return['sale_id'])->where('product_id', $return['product_id'])->increment('quantity', $return['quantity']);
        \App\Models\Product::where('id', $return['product_id'])->decrement('stock', $return['quantity']);
        $debt = SaleDebt::where('id', $this->currentDetail['sale_id'])->where('paid', '!=', 0)->first();

        if (isset($debt['paid'])) {
            \App\Models\Client::where('id', $this->currentClient['id'])->increment('currentBalance', $debt['paid']);
            if ($debt['payment'] == 'cash') {
                \App\Models\Safe::first()->increment('currentBalance', $debt['paid']);
            } else {
                \App\Models\Bank::where('id', $debt['bank_id'])->increment('currentBalance', $debt['paid']);
            }
        }
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);

    }

    public function resetData()
    {
        $this->reset('productName', 'editMode', 'amount', 'quantity', 'price', 'quantityReturn', 'clientSearch', 'currentClient', 'currentDetail', 'currentSale', 'saleSearch', 'return_date');
    }

    public function render()
    {
        if ($this->return_date == '') {
            $this->return_date = date('Y-m-d');
        }
        if ($this->buyer == 'client') {
            $this->clients = \App\Models\Client::where('clientName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        } elseif ($this->buyer == 'supplier') {
            $this->clients = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        }        if (!empty($this->currentClient)) {
            $this->sales = \App\Models\Sale::where('client_id', $this->currentClient['id'])->where('id', 'LIKE', '%' . $this->saleSearch . '%')->get();
        }
        return view('livewire.returns');
    }
}
