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
        $parchase = \App\Models\Purchase::create([
            'supplier_id' => $this->currentSupplier['id'],
            'discount' => $this->discount,
            'paid' => $this->paid,
            'total_amount' => $this->amount,
            'purchase_date' => now()
        ]);

        foreach ($this->cart as $item) {
            PurchaseDetail::create([
                'purchase_id' => $parchase->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'sale_price' => $item['sale_price'],
            ]);
        }

        $this->resetData();
    }

    public function resetData()
    {
        $this->reset('amount', 'discount', 'cart', 'currentSupplier', 'currentProduct');
    }

    public function calcPrice()
    {
        $this->total_amount -= $this->currentProduct['amount'];
        $this->currentProduct['amount'] = floatval($this->currentProduct['sale_price']) * floatval($this->currentProduct['quantity']);
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
        $this->currentProduct['sale_price'] = 0;
        $this->currentProduct['quantity'] = 1;
        $this->currentProduct['amount'] = floatval($this->currentProduct['sale_price']) * floatval($this->currentProduct['quantity']);

    }

    public function add($id)
    {
        $this->cart[$id] = [
            'id' => $this->currentProduct['id'],
            'productName' => $this->currentProduct['productName'],
            'sale_price' => $this->currentProduct['sale_price'],
            'quantity' => $this->currentProduct['quantity'],
            'amount' => $this->currentProduct['amount'],
        ];
//        $this->currentProduct = [];
    }

    public function choosePurchase($purchase)
    {
        $this->cart = $purchase['purchase_details'];
        dd($this->cart);
        $this->currentSupplier = ['id' => $this->purchases[0]['id'], 'name' => $this->purchases[0]['name']];
        $this->editMode = false;


    }

    public function deleteList($id)
    {
        $this->amount -= $this->cart[$id]['amount'];
        unset($this->cart[$id]);
    }


    public function edit($id)

    {
        $this->editMode = true;
        $this->purchases = \App\Models\Supplier::join('purchases', 'suppliers.id', '=', 'purchases.supplier_id')->join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')->join('products', 'purchase_details.product_id', '=', 'products.id')->where('suppliers.id', $id)->select('suppliers.*', 'products.*')->get();
        dd($this->purchases);
//        $this->purchases = \App\Models\Supplier::find($id)->with('purchases.purchaseDetails.product:productName')->get();
//        dd($this->purchases[0]['purchases'][0]['purchaseDetails']);
//        dd($this->purchases[0]);
    }

    public function delete($id)
    {
        $purchase = \App\Models\Purchase::find($id);
        $purchase->delete();
    }

    public function chooseSupplier($supplier = [])
    {
        $this->editMode = false;
        if (empty($supplier)) {
            $this->currentSupplier = [];
        } else {
            $this->currentSupplier = $supplier;
        }
    }


    public function render()
    {
        $this->suppliers = \App\Models\Supplier::where('name', 'LIKE', '%' . $this->supplierSearch . '%')->get();
        $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->search . '%')->get(['id', 'productName'])->keyBy('id');
        return view('livewire.purchase');
    }

}
