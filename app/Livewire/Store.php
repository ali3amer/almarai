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
    public string $storeName = '';
    public  string $search = '';
    public Collection $stores;

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Store::create(['storeName' => $this->storeName]);
            } else {
                $store = \App\Models\Store::find($id);
                $store->storeName = $this->storeName;
                $store->save();
            }
            $this->id = 0;
            $this->storeName = '';
        }

    }

    public function edit($store)
    {
        $this->id = $store['id'];
        $this->storeName = $store['storeName'];
    }

    public function delete($id)
    {
        $store = \App\Models\Store::find($id);
        $store->delete();
    }




    public function render()
    {
        $this->stores = \App\Models\Store::where('storeName', 'LIKE', '%' . $this->search . '%')->get();
        return view('livewire.store');
    }
}
