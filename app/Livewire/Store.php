<?php

namespace App\Livewire;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Store extends Component
{
    use LivewireAlert;

    public string $title = 'المخازن';
    public int $id = 0;
    public string $storeName = '';
    public string $search = '';
    public Collection $stores;

    protected function rules()
    {
        return [
            'storeName' => 'required|unique:stores,storeName,' . $this->id
        ];
    }

    protected function messages()
    {
        return [
            'storeName.required' => 'الرجاء إدخال إسم المخزن',
            'storeName.unique' => 'هذا المخزن موجود مسبقاً'
        ];
    }

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Store::create(['storeName' => $this->storeName]);
                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
            } else {
                $store = \App\Models\Store::find($id);
                $store->storeName = $this->storeName;
                $store->save();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
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
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);

    }


    public function render()
    {
        $this->stores = \App\Models\Store::where('storeName', 'LIKE', '%' . $this->search . '%')->get();
        return view('livewire.store');
    }
}
