<?php

namespace App\Livewire;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Livewire\Forms\ProductForm;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Product extends Component
{
    use WithPagination;
    use LivewireAlert;

    protected $listeners = [
        'delete'
    ];
    public string $title = 'المنتجات';
    public $search = '';
    public $store_id = 0;
    public $category_id = 0;
    public bool $create = false;
    public bool $read = false;
    public bool $update = false;
    public bool $delete = false;

    public Collection $categories;
    public Collection $stores;


    public ProductForm $form;
    public $stock = 0;

    protected function rules()
    {
        return [
            'form.productName' => 'required'
        ];
    }

    protected function messages()
    {
        return [
            'form.productName.required' => 'الرجاء إدخال إسم المنتج',
        ];
    }

    public function mount()
    {
        $user = auth()->user();
        $this->stores = \App\Models\Store::get()->keyBy("id");
        $this->categories = \App\Models\Category::get()->keyBy("id");
        $this->create = $user->hasPermission('products-create');
        $this->read = $user->hasPermission('products-read');
        $this->update = $user->hasPermission('products-update');
        $this->delete = $user->hasPermission('products-delete');
    }

//    public function searchProduct()
//    {
//        if ($this->store_id != 0 && $this->category_id != 0) {
//            $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->search . '%')->where('store_id', $this->store_id)->where('category_id', $this->category_id)->get();
//        } elseif ($this->store_id != 0) {
//            $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->search . '%')->where('store_id', $this->store_id)->get();
//        } elseif ($this->category_id != 0) {
//            $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->search . '%')->where('category_id', $this->category_id)->get();
//        } else {
//            $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->search . '%')->get();
//        }
//    }
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


    public function edit($id)
    {
        $product = \App\Models\Product::find($id);
        $this->form->setProduct($product);
        $this->stock = $product->stock;
    }

    public function deleteMessage($product)
    {
        $this->confirm("  هل توافق على حذف المنتج  " . $product['productName'] . "؟", [
            'inputAttributes' => ["id" => $product['id']],
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
        $this->products = \App\Models\Product::all();

        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
    }

    public function render()
    {
        if ($this->category_id != 0 && $this->store_id != 0) {
            $products = \App\Models\Product::where("productName", "LIKE", "%" . $this->search . "%")->where("category_id", $this->category_id)->where("store_id", $this->store_id)->paginate(10);
        } elseif ($this->category_id != 0) {
            $products = \App\Models\Product::where("productName", "LIKE", "%" . $this->search . "%")->where("category_id", $this->category_id)->paginate(10);
        } elseif ($this->store_id != 0) {
            $products = \App\Models\Product::where("productName", "LIKE", "%" . $this->search . "%")->where("store_id", $this->store_id)->paginate(10);
        } else {
            $products = \App\Models\Product::where("productName", "LIKE", "%" . $this->search . "%")->paginate(10);
        }
        return view('livewire.product', [
            'products' => $products
        ]);
    }
}
