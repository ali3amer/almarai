<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;
use function Symfony\Component\Translation\t;

class Damaged extends Component
{
    public string $title = 'المنتجات التالفه';
    public string $productsSearch = '';
    public int $id = 0;
    #[Rule('required|min:1')]
    public float $quantity = 0;
    public Collection $products;
    public Collection $damageds;
    public array $currentProduct = [];
    public array $currentDamaged = [];

    public function chooseProduct($product)
    {
        $this->currentProduct = $product;
    }

    public function save()
    {
        if ($this->id == 0) {
            \App\Models\Damaged::create([
                'product_id' => $this->currentProduct['id'],
                'quantity' => $this->quantity,
            ]);

            \App\Models\Product::where('id', $this->currentProduct['id'])->decrement('stock', $this->quantity);
            session()->flash('success', 'تم الحفظ');
        } else {
            \App\Models\Damaged::where('id', $this->id)->update([
                'product_id' => $this->currentProduct['id'],
                'quantity' => $this->quantity,
            ]);

            \App\Models\Product::where('id', $this->currentProduct['id'])->increment('stock', $this->currentDamaged['quantity']);
            \App\Models\Product::where('id', $this->currentProduct['id'])->decrement('stock', $this->quantity);
            session()->flash('success', 'تم التعديل بنجاح');
        }

        $this->resetData();
    }

    public function edit($damaged)
    {
        $this->id = $damaged['id'];
        $this->currentDamaged = $damaged;
        $this->quantity = $damaged['quantity'];
        $this->currentProduct = $damaged['product'];
    }

    public function delete($damaged)
    {
        \App\Models\Product::where('id', $damaged['product_id'])->increment('stock', $damaged['quantity']);
        \App\Models\Damaged::where('id', $damaged['id'])->delete();
        session()->flash('success', 'تم الحذف بنجاح');
        $this->resetData();
        return redirect()->to('/damaged');
    }

    public function resetData()
    {
        $this->reset('productsSearch', 'id', 'quantity', 'currentProduct', 'currentDamaged');
    }

    public function render()
    {
        $this->damageds = \App\Models\Damaged::with('product')->get();
        $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productsSearch . '%')->get();
        return view('livewire.damaged');
    }
}
