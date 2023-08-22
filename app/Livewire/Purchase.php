<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Purchase extends Component
{
    public string $title = 'المشتريات';
    public int $id = 0;
    #[Rule('required|min:1')]
    public int $supplier_id = 0;
    #[Rule('required|min:2')]
    public string $total_amount = '';
    #[Rule('required|min:2')]
    public string $purchase_date = '';
    public string $search = '';
    public array $chosenSupplier;
    public Collection $purchases;
    public Collection $suppliers;
    public Collection $products;
    public array $cart = [];
    public float $quantity = 0;
    public float $price = 0;
    public float $amount = 0;
    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Purchase::create(['name' => $this->name, 'phone' => $this->phone]);
            } else {
                $purchase = \App\Models\Client::find($id);
                $purchase->name = $this->name;
                $purchase->phone = $this->phone;
                $purchase->save();
            }
            $this->id = 0;
            $this->name = '';
            $this->phone = '';
        }

    }

    public function calcPrice($item)
    {
        $this->cart[$item]['amount'] = floatval($this->cart[$item]['quantity']) * floatval($this->cart[$item]['price']);
    }

    public function add($product)
    {
        $this->cart[$product['id']] = $product;
        unset($this->products[$product['id']]);
    }

    public function deleteList($item)
    {
        unset($this->cart[$item]);
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

    public function render()
    {
        $this->purchases = \App\Models\Purchase::all();
        $this->suppliers = \App\Models\Supplier::where('name', 'LIKE', '%' . $this->search . '%')->get();
        $this->products = \App\Models\Product::where('name', 'LIKE', '%' . $this->search . '%')->get()->keyBy('id');
        return view('livewire.purchase');
    }
}
