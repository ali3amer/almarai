<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Livewire\Attributes\Rule;
use Livewire\Form;

class ProductForm extends Form
{
    public int $id = 0;
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
    public float $initialStock = 0;
    public $barcode = null;
    public $batch = null;
    public $expired_date = null;

    public function setProduct(Product $product)
    {
        $this->id = $product->id;
        $this->productName = $product->productName;
        $this->unit = $product->unit;
        $this->store_id = $product->store_id;
        $this->category_id = $product->category_id;
        $this->sale_price = $product->sale_price;
        $this->purchase_price = $product->purchase_price;
        $this->initialStock = $product->initialStock;
        $this->batch = $product->batch;
        $this->barcode = $product->barcode;
        $this->expired_date = $product->expired_date;
        $this->adding_date = session("date");
    }
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
