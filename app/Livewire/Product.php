<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;
use function Livewire\store;

class Product extends Component
{
    public string $title = 'المنتجات';
    public int $id = 0;
    #[Rule('required|min:2')]
    public string $productName = '';
    public int $store_id = 0;
    public int $category_id = 0;
    public float $sale_price = 0;
    public string $search = '';
    public Collection $categories;
    public Collection $stores;

    public Collection $products;

    public function save($id)
    {
        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Product::create(['productName' => $this->productName, 'store_id' => $this->store_id, 'category_id' => $this->category_id, 'sale_price' => $this->sale_price]);
            } else {
                $product = \App\Models\Product::find($id);
                $product->productName = $this->productName;
                $product->store_id = $this->store_id;
                $product->category_id = $this->category_id;
                $product->sale_price = $this->sale_price;
                $product->save();
            }
            $this->productName = '';
            $this->category_id = 0;
            $this->sale_price = 0;
        }
    }


    public function edit($product)
    {
        $this->id = $product['id'];
        $this->productName = $product['productName'];
        $this->store_id = $product['store_id'];
        $this->category_id = $product['category_id'];
        $this->sale_price = $product['sale_price'];
    }

    public function delete($id)
    {
        $product = \App\Models\Product::find($id);
        $product->delete();
    }

    public function searchProduct()
    {
        if ($this->store_id != 0 && $this->category_id != 0) {
            $this->products = \App\Models\Product::with('category', 'store')->where('productName', 'LIKE', '%' . $this->search . '%')->where('store_id', $this->store_id)->where('category_id', $this->category_id)->get();
            dd($this->products);

        } elseif ($this->store_id != 0) {
            $this->products = \App\Models\Product::with('category', 'store')->where('store_id', $this->store_id)->get();
        }
        if ($this->category_id != 0) {
            $this->products = \App\Models\Product::with('category', 'store')->where('category_id', $this->category_id)->get();
        } else {
            $this->products = \App\Models\Product::with('category', 'store')->get();
        }
    }

    public function render()
    {
        $this->stores = \App\Models\Store::all();
        $this->categories = \App\Models\Category::all();
        return view('livewire.product');
    }
}
