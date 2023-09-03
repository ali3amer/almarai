<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Livewire\Attributes\Rule;
use Livewire\Form;

class ProductForm extends Form
{
    public int $id = 0;
    #[Rule('required|min:2')]
    public string $productName = '';

    #[Rule('required|min:1')]
    public string $unit = '';
    #[Rule('required|numeric|min:1')]
    public int $store_id = 0;
    #[Rule('required|numeric|min:1')]
    public int $category_id = 0;
    #[Rule('required|numeric|min:1')]
    public float $sale_price = 0;
    #[Rule('required|numeric|min:1')]
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
