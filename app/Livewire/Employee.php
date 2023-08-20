<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Employee extends Component
{

    public string $title = 'الموظفين';
    public int $id = 0;
    #[Rule('required|min:2')]
    public string $name = '';
    #[Rule('required|min:2')]
    public float $salary = 0;
    public  string $search = '';
    public Collection $employees;

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Employee::create(['name' => $this->name, 'salary' => $this->salary]);
            } else {
                $employee = \App\Models\Employee::find($id);
                $employee->name = $this->name;
                $employee->salary = $this->salary;
                $employee->save();
            }
            $this->id = 0;
            $this->name = '';
            $this->salary = 0;
        }

    }

    public function edit($employee)
    {
        $this->id = $employee['id'];
        $this->name = $employee['name'];
        $this->salary = $employee['salary'];
    }

    public function delete($id)
    {
        $employee = \App\Models\Employee::find($id);
        $employee->delete();
    }



    public function render()
    {
        $this->employees = \App\Models\Employee::where('name', 'like', '%' . $this->search . '%')->get();

        return view('livewire.employee');
    }
}
