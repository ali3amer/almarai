<?php

namespace App\Livewire;

use App\Livewire\Forms\ProductForm;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;
use function Livewire\store;

class Product extends Component
{
    public string $title = 'المنتجات';
    public string $search = '';
    public int $store_id = 0;
    public int $category_id = 0;
    public Collection $categories;
    public Collection $stores;

    public Collection $products;

    public ProductForm $form;

    protected function rules() {
        return [
            'form.productName' => 'required|unique:products,productName,'.$this->form->id
        ];
    }

    protected function messages() {
        return [
            'form.productName.required' => 'الرجاء إدخال إسم المنتج',
            'form.productName.unique' => 'هذا المنتج موجود مسبقاً'
        ];
    }

    public function save($id)
    {
        if ($this->validate()) {
            if ($this->form->id == 0) {
                $this->form->store();
                session()->flash('success', 'تمت الاضافه بنجاح');
            } else {
                $this->form->update();
                session()->flash('success', 'تم التعديل بنجاح');
            }
        }
    }


    public function edit($product)
    {
        $this->form->id = $product['id'];
        $this->form->category_id = $product['category_id'];
        $this->form->store_id = $product['store_id'];
        $this->form->productName = $product['productName'];
        $this->form->sale_price = $product['sale_price'];
        $this->form->purchase_price = $product['purchase_price'];
        $this->form->unit = $product['unit'];
    }

    public function delete($id)
    {
        $product = \App\Models\Product::find($id);
        $product->delete();
        session()->flash('success', 'تم الحذف بنجاح');
    }

    public function render()
    {
        if ($this->store_id != 0 && $this->category_id != 0) {
            $this->products = \App\Models\Product::join('categories', 'products.category_id', '=', 'categories.id')->join('stores', 'products.store_id', '=', 'stores.id')->select('products.*', 'categories.categoryName', 'stores.storeName')->where('productName', 'LIKE', '%' . $this->search . '%')->where('store_id', $this->store_id)->where('category_id', $this->category_id)->get();
        } elseif ($this->store_id != 0) {
            $this->products = \App\Models\Product::join('categories', 'products.category_id', '=', 'categories.id')->join('stores', 'products.store_id', '=', 'stores.id')->select('products.*', 'categories.categoryName', 'stores.storeName')->where('productName', 'LIKE', '%' . $this->search . '%')->where('store_id', $this->store_id)->get();
        } elseif ($this->category_id != 0) {
            $this->products = \App\Models\Product::join('categories', 'products.category_id', '=', 'categories.id')->join('stores', 'products.store_id', '=', 'stores.id')->select('products.*', 'categories.categoryName', 'stores.storeName')->where('productName', 'LIKE', '%' . $this->search . '%')->where('category_id', $this->category_id)->get();
        } else {
            $this->products = \App\Models\Product::join('categories', 'products.category_id', '=', 'categories.id')->join('stores', 'products.store_id', '=', 'stores.id')->select('products.*', 'categories.categoryName', 'stores.storeName')->where('productName', 'LIKE', '%' . $this->search . '%')->get();
        }
        $this->stores = \App\Models\Store::all();
        $this->categories = \App\Models\Category::all();
        return view('livewire.product');
    }
}
