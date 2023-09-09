<?php

namespace App\Livewire;

use App\Models\PurchaseDebt;
use App\Models\PurchaseDetail;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Purchase extends Component
{
    public string $title = 'المشتريات';
    public int $id = 0;
    public string $modalName = '';

    public string $search = '';
    public Collection $purchases;
    public Collection $suppliers;
    public Collection $products;
    public string $productSearch = '';
    public string $supplierSearch = '';

    public float $total_amount = 0;
    public $paid = 0;

    public array $currentSupplier = [];
    public array $oldQuantities = [];
    public array $currentProduct = [];
    public array $cart = [];
    public string $purchaseSearch = '';
    public string $purchase_date = '';
    public string $payment = 'cash';
    public string $bank = '';
    public $remainder = 0;

    public function save()
    {
        if ($this->id == 0) {
            $purchase = \App\Models\Purchase::create([
                'supplier_id' => $this->currentSupplier['id'],
                'total_amount' => $this->total_amount,
                'purchase_date' => $this->purchase_date,
            ]);

            PurchaseDebt::create([
                'purchase_id' => $purchase['id'],
                'paid' => $this->paid,
                'bank' => $this->bank,
                'payment' => $this->payment,
                'remainder' => $this->remainder,
                'due_date' => $purchase['purchase_date']
            ]);

            foreach ($this->cart as $item) {
                PurchaseDetail::create([
                    'purchase_id' => $purchase['id'],
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['purchase_price'],
                ]);
                \App\Models\Product::where('id', $item['id'])->increment('stock', $item['quantity']);
            }
            session()->flash('success', 'تم الحفظ بنجاح');

        } else {
            \App\Models\Purchase::where('id', $this->id)->update([
                'total_amount' => $this->total_amount,
                'purchase_date' => $this->purchase_date
            ]);

            PurchaseDebt::where('purchase_id', $this->id)->first()->update([
                'purchase_id' => $this->id,
                'paid' => $this->paid,
                'bank' => $this->bank,
                'payment' => $this->payment,
                'remainder' => $this->remainder,
                'due_date' => $this->purchase_date
            ]);
            PurchaseDetail::where('purchase_id', $this->id)->delete();

            foreach ($this->oldQuantities as $key => $quantity) {
                \App\Models\Product::where('id', $key)->decrement('stock', $quantity);
            }

            foreach ($this->cart as $item) {
                PurchaseDetail::create([
                    'purchase_id' => $this->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['purchase_price'],
                ]);
                \App\Models\Product::where('id', $item['id'])->increment('stock', $item['quantity']);
            }
            session()->flash('success', 'تم التعديل بنجاح');


        }
        $this->resetData();

    }

    public function edit($purchase)
    {

    }

    public function delete($id)
    {

    }

    public function chooseSupplier($supplier)
    {
        $this->currentSupplier = [];
        $this->currentSupplier = $supplier;
    }

    public function chooseProduct($product)
    {
        $this->currentProduct = $product;
        $this->currentProduct['quantity'] = 1;
        $this->currentProduct['amount'] = $product['purchase_price'];
        $this->productSearch = '';
    }

    public function calcCurrentProduct()
    {

        $this->currentProduct['amount'] = floatval($this->currentProduct['purchase_price']) * floatval($this->currentProduct['quantity']);
    }


    public function addToCart()
    {
        $this->cart[$this->currentProduct['id']] = $this->currentProduct;
        $this->cart[$this->currentProduct['id']]['amount'] = $this->currentProduct['purchase_price'] * $this->currentProduct['quantity'];
        $this->total_amount += $this->cart[$this->currentProduct['id']]['amount'];
        $this->currentProduct = [];
    }

    public function deleteFromCart($id)
    {
        $this->total_amount -= $this->cart[$id]['amount'];
        $this->calcDiscount();
        $this->calcRemainder();
        unset($this->cart[$id]);
        if (empty($this->cart)) {
            $this->remainder = 0;
            $this->paid = 0;
        }
    }


    public function calcRemainder()
    {
        $this->remainder = $this->total_amount - floatval($this->paid);
    }

    public function choosePurchase($purchase)
    {
        $this->total_amount = $purchase['total_amount'];
        $this->paid = $purchase['purchase_debts'][0]['paid'];
        $this->payment = $purchase['purchase_debts'][0]['payment'];
        $this->bank = $purchase['purchase_debts'][0]['bank'];
        $this->purchase_date = $purchase['purchase_date'];
        $this->id = $purchase['id'];
        foreach ($purchase['purchase_details'] as $detail) {
            $this->cart[$detail['product_id']] = [
                'id' => $detail['product_id'],
                'purchase_id' => $detail['purchase_id'],
                'product_id' => $detail['product_id'],
                'productName' => $detail['product']['productName'],
                'quantity' => $detail['quantity'],
                'purchase_price' => $detail['price'],
                'amount' => $detail['price'] * $detail['quantity'],
            ];

            $this->oldQuantities[$detail['product_id']] = $detail['quantity'];
        }

    }

    public function resetData()
    {
        $this->reset('currentSupplier', 'currentProduct', 'cart', 'search', 'supplierSearch', 'paid', 'remainder', 'total_amount', 'id', 'oldQuantities');
    }

    public function render()
    {
        if (!empty($this->currentSupplier)) {
            $this->purchases = \App\Models\Purchase::where('supplier_id', $this->currentSupplier['id'])
                ->where('id', 'LIKE', '%' . $this->purchaseSearch . '%')->orWhere('purchase_date', 'LIKE', '%' . $this->purchaseSearch . '%')
                ->with('purchaseDetails.product', 'purchaseDebts')->get();
//            dd($this->purchases);
        } else {
            $this->purchase_date = date('Y-m-d');
        }
        $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();
        $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->get();
        return view('livewire.purchase');
    }

}
