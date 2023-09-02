<?php

namespace App\Livewire;

use App\Models\PurchaseDetail;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Purchase extends Component
{
    public string $title = 'المشتريات';
    public string $name = '';
    public int $id = 0;
    #[Rule('required|min:1')]
    public int $supplier_id = 0;
    #[Rule('required|min:2')]
    public float $total_amount = 0;
    #[Rule('required|min:2')]
    public string $purchase_date = '';
    public bool $editMode = false;
    public string $supplierSearch = '';
    public string $search = '';
    public string $searchPurchase = '';
    public Collection $purchases;
    public Collection $suppliers;
    public Collection $products;
    public array $cart = [];
    public float $discount = 0;
    public float $paid = 0;
    public float $amount = 0;
    public array $currentSupplier = [];
    public array $currentProduct = [];

    public function save()
    {
        if($this->id == 0) {
            $parchase = \App\Models\Purchase::create([
                'supplier_id' => $this->currentSupplier['id'],
                'discount' => $this->discount,
                'paid' => $this->paid,
                'total_amount' => $this->total_amount,
                'purchase_date' => now()
            ]);

            foreach ($this->cart as $item) {
                PurchaseDetail::create([
                    'purchase_id' => $parchase->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
        } else {
            $purchase = \App\Models\Purchase::find($this->id)->update([
                'discount' => $this->discount,
                'paid' => $this->paid,
                'total_amount' => $this->total_amount,
                'purchase_date' => now()
            ]);

            PurchaseDetail::where('purchase_id', $this->id)->delete();

            foreach ($this->cart as $item) {
                PurchaseDetail::create([
                    'purchase_id' => $this->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
        }

        $this->resetData();
    }

    public function resetData()
    {
        $this->reset('amount', 'discount', 'cart', 'currentSupplier', 'currentProduct', 'id', 'total_amount', 'editMode');
    }

    public function calcPrice()
    {
        $this->total_amount -= $this->currentProduct['amount'];
        $this->currentProduct['amount'] = floatval($this->currentProduct['price']) * floatval($this->currentProduct['quantity']);
        $this->total_amount += $this->currentProduct['amount'];
    }

    public function calcDiscount()
    {
        $this->paid = $this->total_amount - $this->discount;
    }

    public function chooseProduct($product)
    {
        $this->currentProduct = [];
        $this->currentProduct = $product;
        $this->currentProduct['price'] = 0;
        $this->currentProduct['quantity'] = 1;
        $this->currentProduct['amount'] = floatval($this->currentProduct['price']) * floatval($this->currentProduct['quantity']);

    }

    public function add($id)
    {
        $this->cart[$id] = [
            'id' => $this->currentProduct['id'],
            'productName' => $this->currentProduct['productName'],
            'price' => $this->currentProduct['price'],
            'quantity' => $this->currentProduct['quantity'],
            'amount' => $this->currentProduct['amount'],
        ];
        $this->calcDiscount();
//        $this->currentProduct = [];
    }

    public function choosePurchase($purchase)
    {
        $this->total_amount = $purchase['total_amount'];
        $this->discount = $purchase['discount'];
        $this->paid = $purchase['paid'];
        $this->id = $purchase['id'];
        $this->currentSupplier = $purchase['supplier'];
        foreach ($purchase['purchase_details'] as $item) {
            $this->cart[$item['product_id']] = [
                'id' => $item['product_id'],
                'productName' => $item['product']['productName'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'amount' => $item['quantity'] * $item['price'],
            ];
        }

        $this->editMode = false;


    }

    public function deleteList($id)
    {
        $this->total_amount -= $this->cart[$id]['amount'];
        $this->calcDiscount();
        unset($this->cart[$id]);
    }


    public function edit($supplier)

    {
        $this->currentSupplier = $supplier;
        $this->editMode = true;
        $this->purchases = \App\Models\Purchase::with('purchaseDetails.product', 'supplier')->where('supplier_id', $supplier['id'])->get();
    }

    public function purchaseSearch()
    {

        $this->purchases = \App\Models\Purchase::with('purchaseDetails.product', 'supplier')
            ->join('suppliers',  'suppliers.id', '=', 'purchases.supplier_id')->select('purchases.*', 'suppliers.supplierName')->where('supplier_id', $this->currentSupplier['id'])->where('purchases.id', $this->searchPurchase)->get();
    }

    public function delete($id)
    {
        $purchase = \App\Models\Purchase::find($id);
        $purchase->delete();
    }

    public function chooseSupplier($supplier = [])
    {
        $this->resetData();
        if (empty($supplier)) {
            $this->currentSupplier = [];
        } else {
            $this->currentSupplier = $supplier;
        }
    }


    public function render()
    {
        $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();
        $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->search . '%')->get(['id', 'productName'])->keyBy('id');
        return view('livewire.purchase');
    }

}
