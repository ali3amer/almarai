<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Supplier extends Component
{
    public string $title = 'الموردين';
    public int $id = 0;
    #[Rule('required|min:2')]
    public string $name = '';
    #[Rule('required|min:2')]
    public string $phone = '';
    #[Rule('required|min:2')]
    public string $address = '';
    public string $search = '';
    public Collection $suppliers;

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Supplier::create(['name' => $this->name, 'phone' => $this->phone, 'address' => $this->address,]);
            } else {
                $supplier = \App\Models\Supplier::find($id);
                $supplier->name = $this->name;
                $supplier->phone = $this->phone;
                $supplier->address = $this->address;
                $supplier->save();
            }
            $this->id = 0;
            $this->name = '';
            $this->phone = '';
            $this->address = '';
        }

    }

    public function edit($supplier)
    {
        $this->id = $supplier['id'];
        $this->name = $supplier['name'];
    }

    public function delete($id)
    {
        $supplier = \App\Models\Supplier::find($id);
        $supplier->delete();
    }

    public function render()
    {
        $this->suppliers = \App\Models\Supplier::where('name', 'like', '%' . $this->search . '%')->get();
        return view('livewire.supplier');
    }
}
