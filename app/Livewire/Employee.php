<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\EmployeeGift;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Employee extends Component
{

    public string $title = 'الموظفين';
    public int $id = 0;
    public int $bank_id = 0;
    #[Rule('required|min:2')]
    public string $employeeName = '';
    #[Rule('required|min:2')]
    public float $salary = 0;
    public float $total_sum_paid = 0;
    public string $search = '';
    public string $saleSearch = '';
    public string $gift_date = '';
    public string $bank = '';
    public string $payment = 'cash';
    public string $note = '';
    public $gift_amount = 0;

    public array $currentEmployee = [];
    public bool $editMode = false;
    public bool $editGiftMode = false;
    public Collection $employees;
    public Collection $gifts;
    public Collection $banks;
    public Collection $sales;
    public array $currentGift = [];

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Employee::create(['employeeName' => $this->employeeName, 'salary' => $this->salary]);
            } else {
                $employee = \App\Models\Employee::find($id);
                $employee->employeeName = $this->employeeName;
                $employee->salary = $this->salary;
                $employee->save();
            }
            $this->id = 0;
            $this->employeeName = '';
            $this->salary = 0;
        }

    }

    public function edit($employee)
    {
        $this->editMode = true;
        $this->id = $employee['id'];
        $this->employeeName = $employee['employeeName'];
        $this->salary = $employee['salary'];
    }

    public function delete($id)
    {
        $employee = \App\Models\Employee::find($id);
        $employee->delete();
    }

    public function getGifts($employee)
    {
        $this->currentEmployee = $employee;
        $this->gift_date = date('Y-m-d');
        $this->gifts = EmployeeGift::where('employee_id', $this->currentEmployee['id'])->get();
        $this->getSales();
    }

    public function getSales()
    {
        $this->sales = \App\Models\Sale::where('employee_id', $this->currentEmployee['id'])->withSum('saleDebts', 'paid')
            ->with('saleDetails.product', 'saleDebts')->where('id', 'LIKE', '%'.$this->saleSearch.'%')->get();
        $this->total_sum_paid = 0;
        dd($this->sales);
        foreach ($this->sales as $sale) {
            $this->total_sum_paid += floatval($sale->total_amount) - floatval($sale->sale_debts_sum_paid);
        }
    }

    public function payGift()
    {
        EmployeeGift::create([
            'employee_id' => $this->currentEmployee['id'],
            'payment' => $this->payment,
            'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
            'gift_amount' => $this->gift_amount,
            'gift_date' => $this->gift_date,
            'note' => $this->note
        ]);

        if ($this->payment == 'cash') {
            \App\Models\Safe::first()->decrement('currentBalance', $this->gift_amount);
        } else {
            Bank::where('id', $this->bank_id)->decrement('currentBalance', $this->gift_amount);
        }

        $this->getGifts($this->currentEmployee);
        session()->flash('success', 'تم الدفع بنجاح');

    }

    public function editGift($gift)
    {
        $this->editGiftMode = true;
        $this->currentGift = $gift;
        $this->gift_date = $this->currentGift['gift_date'];
        $this->gift_amount = $this->currentGift['gift_amount'];
        $this->note = $this->currentGift['note'];
    }

    public function updateGift($id)
    {
        $gift = EmployeeGift::where('id', $id)->first();

        if ($gift['payment'] == 'cash') {
            \App\Models\Safe::first()->increment('currentBalance', $gift['gift_amount']);
        } else {
            Bank::where('id', $gift['bank_id'])->increment('currentBalance', $gift['gift_amount']);
        }

        $gift->update([
            'gift_amount' => $this->gift_amount,
            'gift_date' => $this->gift_date,
            'note' => $this->note
        ]);


        if ($this->payment == 'cash') {
            \App\Models\Safe::first()->decrement('currentBalance', $this->gift_amount);
        } else {
            Bank::where('id', $this->bank_id)->decrement('currentBalance', $this->gift_amount);
        }

        $this->getGifts($this->currentEmployee);
        $this->gift_date = date('Y-m-d');
        $this->reset('currentGift', 'editGiftMode', 'gift_amount', 'note');
        session()->flash('success', 'تم التعديل بنجاح');
    }

    public function deleteGift($id)
    {
        $gift = EmployeeGift::where('id', $id)->first();
        if ($gift['payment'] == 'cash') {
            \App\Models\Safe::first()->increment('currentBalance', $gift['gift_amount']);
        } else {
            Bank::where('id', $gift['bank_id'])->increment('currentBalance', $gift['gift_amount']);
        }
        $gift->delete();
        $this->getGifts($this->currentEmployee);
        session()->flash('success', 'تم الحذف بنجاح');
    }

    public function resetData()
    {
        $this->reset('id', 'employeeName', 'id', 'salary', 'editMode', 'currentEmployee');
    }

    public function render()
    {
        $this->employees = \App\Models\Employee::where('employeeName', 'like', '%' . $this->search . '%')->get();
        $this->banks = Bank::all();
        return view('livewire.employee');
    }
}
