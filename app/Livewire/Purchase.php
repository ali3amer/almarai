<?php

namespace App\Livewire;

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
    public $discount = 0;
    public float $paid = 0;

    public array $currentSupplier = [];
    public array $oldQuantities = [];
    public array $currentProduct = [];
    public array $cart = [];

    public function save()
    {
        if ($this->id == 0) {
            $purchase = \App\Models\Purchase::create([
                'supplier_id' => $this->currentSupplier['id'],
                'paid' => $this->paid,
                'discount' => $this->discount,
                'total_amount' => $this->total_amount,
                'purchase_date' => now(),
            ]);

            foreach ($this->cart as $item) {
                PurchaseDetail::create([
                    'purchase_id' => $purchase['id'],
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['sale_price'],
                ]);
                \App\Models\Product::where('id', $item['id'])->increment('stock', $item['quantity']);
            }
            session()->flash('success', 'تم الحفظ بنجاح');

        } else {
            \App\Models\Purchase::where('id', $this->id)->update([
                'paid' => $this->paid,
                'discount' => $this->discount,
                'total_amount' => $this->total_amount,
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
                    'price' => $item['sale_price'],
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

    public function getPurchases()
    {
        $this->purchases = \App\Models\Purchase::where('supplier_id', $this->currentSupplier['id'])->with('purchaseDetails.product')->get();
    }

    public function choosePurchase($purchase)
    {
        $this->total_amount = $purchase['total_amount'];
        $this->discount = $purchase['discount'];
        $this->paid = $purchase['paid'];
        $this->id = $purchase['id'];
        foreach ($purchase['sale_details'] as $detail) {
            $this->cart[$detail['product_id']] = [
                'id' => $detail['product_id'],
                'purchase_id' => $detail['purchase_id'],
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
        $this->reset('currentSupplier', 'currentProduct', 'cart', 'search', 'supplierSearch', 'discount', 'paid', 'total_amount', 'id', 'oldQuantities');
    }

    public function render()
    {
        $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();
        $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->get();
        return view('livewire.purchase');
    }

}
