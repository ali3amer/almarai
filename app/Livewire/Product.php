<?php

namespace App\Livewire;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Livewire\Forms\ProductForm;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;
use function Livewire\store;

class Product extends Component
{
    use LivewireAlert;
    protected $listeners = [
        'delete'
    ];
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
                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
            } else {
                $this->form->update();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
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

    public function deleteMessage($product)
    {
        $this->confirm("  هل توافق على حذف المنتج  " . $product['productName'] .  "؟", [
            'inputAttributes' => ["id"=>$product['id']],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "delete",
            "value" => $product['id'],
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }

    public function delete($data)
    {
        $product = \App\Models\Product::find($data['inputAttributes']['id']);
        $product->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
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
