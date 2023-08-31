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
                'price' => $item['price'],
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
//        $this->currentProduct = [];
    }

    public function choosePurchase($purchase)
    {
        $this->currentSupplier = $purchase['supplier'];
        foreach ($purchase['purchase_details'] as $item) {
            $this->cart[$item['id']] = [
                'id' => $item['product_id'],
                'productName' => $item['product']['productName'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
            ];
        }

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
        $this->purchases = \App\Models\Purchase::with('purchaseDetails.product', 'supplier')->where('supplier_id', $id)->get();
    }

    public function delete($id)
    {
        $purchase = \App\Models\Purchase::find($id);
        $purchase->delete();
    }

    public function chooseSupplier($supplier = [])
    {
        $this->cart = [];
        $this->editMode = false;
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
