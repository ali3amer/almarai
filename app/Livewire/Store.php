<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Store extends Component
{

    public string $title = 'المخازن';
    public int $id = 0;
    #[Rule('required|min:2')]
    public string $name = '';
    public  string $search = '';
    public Collection $stores;

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Store::create(['name' => $this->name]);
            } else {
                $store = \App\Models\Store::find($id);
                $store->name = $this->name;
                $store->save();
            }
            $this->id = 0;
            $this->name = '';
        }

    }

    public function edit($store)
    {
        $this->id = $store['id'];
        $this->name = $store['name'];
    }

    public function delete($id)
    {
        $store = \App\Models\Store::find($id);
        $store->delete();
    }




    public function render()
    {
        $this->stores = \App\Models\Store::where('name', 'LIKE', '%' . $this->search . '%')->get();
        return view('livewire.store');
    }
}
