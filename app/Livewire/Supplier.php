<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Supplier extends Component
{
    public string $title = 'الموردين';
    public int $id = 0;
    public string $supplierName = '';
    #[Rule('required|min:2', message: 'الرجاءإدخال رقم الهاتف')]
    public string $phone = '';
    #[Rule('required|min:2', message: 'الرجاءإدخال العنوان')]
    public string $address = '';
    public $initialBalance = 0;
    public string $search = '';
    public Collection $suppliers;

    protected function rules() {
        return [
            'supplierName' => 'required|unique:suppliers,supplierName,'.$this->id
        ];
    }

    protected function messages() {
        return [
            'supplierName.required' => 'الرجاء إدخال إسم المورد',
            'supplierName.unique' => 'هذا المورد موجود مسبقاً'
        ];
    }

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Supplier::create(['supplierName' => $this->supplierName, 'phone' => $this->phone, 'address' => $this->address, 'initialBalance' => $this->initialBalance, 'currentBalance' => $this->initialBalance]);
                session()->flash('success', 'تمت الاضافه بنجاح');
            } else {
                $supplier = \App\Models\Supplier::find($id);
                $supplier->supplierName = $this->supplierName;
                $supplier->phone = $this->phone;
                $supplier->address = $this->address;
                $supplier->initialBalance = $this->initialBalance;
                $supplier->currentBalance = $this->initialBalance;
                $supplier->save();
                session()->flash('success', 'تم التعديل بنجاح');
            }
            $this->id = 0;
            $this->supplierName = '';
            $this->phone = '';
            $this->address = '';
            $this->initialBalance = '';
        }

    }

    public function edit($supplier)
    {
        $this->id = $supplier['id'];
        $this->supplierName = $supplier['supplierName'];
        $this->address = $supplier['address'];
        $this->phone = $supplier['phone'];
        $this->initialBalance = $supplier['initialBalance'];
    }

    public function delete($id)
    {
        $supplier = \App\Models\Supplier::find($id);
        $supplier->delete();
        session()->flash('success', 'تم الحذف بنجاح');

    }

    public function render()
    {
        $this->suppliers = \App\Models\Supplier::where('supplierName', 'like', '%' . $this->search . '%')->get();
        return view('livewire.supplier');
    }
}
