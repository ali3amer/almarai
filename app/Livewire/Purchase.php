<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Purchase extends Component
{
    public string $title = 'المشتريات';
    public int $id = 0;
    #[Rule('required|min:2')]
    public string $name = '';
    #[Rule('required|min:2')]
    public string $phone = '';
    public string $search = '';
    public Collection $purchases;

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Purchase::create(['name' => $this->name, 'phone' => $this->phone]);
            } else {
                $purchase = \App\Models\Client::find($id);
                $purchase->name = $this->name;
                $purchase->phone = $this->phone;
                $purchase->save();
            }
            $this->id = 0;
            $this->name = '';
            $this->phone = '';
        }

    }

    public function edit($purchase)
    {
        $this->id = $purchase['id'];
        $this->name = $purchase['name'];
        $this->phone = $purchase['phone'];
    }

    public function delete($id)
    {
        $purchase = \App\Models\Purchase::find($id);
        $purchase->delete();
    }

    public function render()
    {
        $this->purchases = \App\Models\Purchase::all();
        return view('livewire.purchase');
    }
}
