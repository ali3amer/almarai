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

        $this->reset('amount', 'discount', 'cart', 'currentSupplier', 'currentProduct');

    }

    public function calcPrice()
    {
        $this->amount -= $this->currentProduct['amount'];
        $this->currentProduct['amount'] = floatval($this->currentProduct['price']) * floatval($this->currentProduct['quantity']);
        $this->amount += $this->currentProduct['amount'];
    }

    public function calcDiscount()
    {
        $this->paid = $this->amount - $this->discount;

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
            'name' => $this->currentProduct['name'],
            'price' => $this->currentProduct['price'],
            'quantity' => $this->currentProduct['quantity'],
            'amount' => $this->currentProduct['amount'],
        ];
//        $this->currentProduct = [];
    }


    public function deleteList($id)
    {
        $this->amount -= $this->cart[$id]['amount'];
        unset($this->cart[$id]);
    }


    public function edit($purchase)
    {
        $this->id = $purchase['id'];
        $this->name = $purchase['name'];
        $this->phone = $purchase['phone'];
    }

    public function delete($id)
    {
        $purchase = \App\Models\Purchase::find($id);
        $purchase->delete();
    }

    public function chooseSupplier($supplier = [])
    {
        if (empty($supplier)) {
            $this->currentSupplier = [];
        } else {
            $this->currentSupplier = $supplier;
        }
    }


    public function render()
    {
        $this->purchases = \App\Models\Purchase::all();
        $this->suppliers = \App\Models\Supplier::where('name', 'LIKE', '%' . $this->supplierSearch . '%')->get();
        $this->products = \App\Models\Product::where('name', 'LIKE', '%' . $this->search . '%')->get(['id', 'name'])->keyBy('id');
        return view('livewire.purchase');
    }
}
