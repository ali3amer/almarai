<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Sale extends Component
{

    public string $title = 'المبيعات';
    public int $id = 0;
    #[Rule('required|min:2')]
    public string $name = '';
    #[Rule('required|min:2')]
    public string $phone = '';
    public string $search = '';
    public Collection $sales;

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Sale::create(['name' => $this->name, 'phone' => $this->phone]);
            } else {
                $sale = \App\Models\Sale::find($id);
                $sale->name = $this->name;
                $sale->phone = $this->phone;
                $sale->save();
            }
            $this->id = 0;
            $this->name = '';
            $this->phone = '';
        }

    }

    public function edit($sale)
    {
        $this->id = $sale['id'];
        $this->name = $sale['name'];
        $this->phone = $sale['phone'];
    }

    public function delete($id)
    {
        $sale = \App\Models\Sale::find($id);
        $sale->delete();
    }

    public function render()
    {
        $this->sales = \App\Models\Sale::all();
        return view('livewire.sale');
    }
}
