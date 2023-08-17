<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Product extends Component
{
    public string $title = 'المنتجات';
    public int $id = 0;
    #[Rule('required|min:2')]
    public string $name = '';
    public int $category_id = 0;
    public float $price = 0;
    public string $search = '';
    public Collection $products;
    public Collection $categories;

    public function save($id)
    {
        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Product::create(['name' => $this->name, 'category_id' => $this->category_id, 'price' => $this->price]);
            } else {
                $product = \App\Models\Product::find($id);
                $product->name = $this->name;
                $product->category_id = $this->category_id;
                $product->price = $this->price;
                $product->save();
            }
            $this->name = '';
            $this->category_id = 0;
            $this->price = 0;
            $this->resetProducts();
        }
    }

    public function resetProducts()
    {
        $this->products = new Collection();
    }

    public function edit($product)
    {
        $this->id = $product['id'];
        $this->name = $product['name'];
        $this->category_id = $product['category_id'];
        $this->price = $product['price'];
    }

    public function delete($id)
    {
        $product = \App\Models\Product::find($id);
        $product->delete();
        $this->resetProducts();
    }

    public function render()
    {
        $this->categories = \App\Models\Category::all();
        $this->products = \App\Models\Product::with('category')->where('name', 'LIKE', '%' . $this->search . '%')->get();
        return view('livewire.product');
    }
}
