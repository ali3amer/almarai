<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Livewire\Attributes\Rule;
use Livewire\Form;

class ProductForm extends Form
{
    public int $id = 0;
    #[Rule('required|min:2', message: 'أدخل إسم المنتج')]
    public string $productName = '';

    public string|null $unit = null;
    #[Rule('required|numeric|min:1', message: 'حدد المخزن')]

    public int $store_id = 0;
    #[Rule('required|numeric|min:1', message: 'حدد القسم')]
    public int $category_id = 0;
    #[Rule('required|numeric|min:1', message: 'أدخل سعر البيع')]
    public float $sale_price = 0;
    #[Rule('required|numeric|min:1', message: 'أدخل سعر الشراء (الجرد)')]
    public float $purchase_price = 0;

    public function store()
    {
        Product::create($this->all());
        $this->reset();
    }

    public function update()
    {
        Product::find($this->id)->update($this->all());
        $this->reset();

    }

}
