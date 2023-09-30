<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\EmployeeGift;
use App\Models\SaleDebt;
use App\Models\SaleDetail;
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
    public float $total_debts = 0;
    public float $total_sum_paid = 0;
    public array $debts = [];
    public array $oldDebts = [];
    public Collection $details;
    public string $search = '';
    public string $saleSearch = '';
    public string $gift_date = '';
    public string $bank = '';
    public string $payment = 'cash';
    public string $note = '';
    public $gift_amount = 0;

    public array $currentEmployee = [];
    public array $claimsArray = [];
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
        $this->gift_amount = $this->currentEmployee['salary'];
        $this->gifts = EmployeeGift::where('employee_id', $this->currentEmployee['id'])->get();
        $this->getSales();
    }

    public function getSales()
    {
        $this->sales = \App\Models\Sale::where('employee_id', $this->currentEmployee['id'])->get()->keyBy('id');
                $this->total_sum_paid = 0;
        foreach ($this->sales as $sale) {
            $this->total_sum_paid += floatval($sale->saleDebts->last()->remainder);
        }
    }

    public function addDebt($sale)
    {
        $remainder = $this->editGiftMode ? 'paid' : 'remainder';
        if (end($sale['sale_debts'])[$remainder] != 0 && !key_exists($sale['id'], $this->debts) && $this->gift_amount > end($sale['sale_debts'])[$remainder]) {
            $this->debts[$sale['id']] = end($sale['sale_debts'])[$remainder];
            $this->gift_amount -= end($sale['sale_debts'])[$remainder];
            $this->calcDebts();
        }
    }

    public function deleteDebt($key)
    {
        $this->gift_amount += $this->debts[$key];
        unset($this->debts[$key]);
        $this->calcDebts();
    }

    public function calcDebts()
    {
        $this->total_debts = 0;
        foreach ($this->debts as $debt) {
            $this->total_debts += $debt;
        }
    }

    public function showSale($sale)
    {
        $this->details = SaleDetail::where('sale_id', $sale['id'])->get();
    }

    public function payGift()
    {
        if ($this->gift_amount != 0) {
            $gift = EmployeeGift::create([
                'employee_id' => $this->currentEmployee['id'],
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'gift_amount' => $this->gift_amount,
                'gift_date' => $this->gift_date,
                'note' => $this->note
            ]);
        }

        if (!empty($this->debts)) {
            foreach ($this->debts as $key => $debt) {
                SaleDebt::create([
                    'sale_id' => $key,
                    'paid' => $debt,
                    'bank' => '',
                    'payment' => 'cash',
                    'remainder' => 0,
                    'client_balance' => $gift['id'],
                    'due_date' => $this->gift_date
                ]);
            }
        }

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

        $saleDebts = SaleDebt::where('client_balance', $this->currentGift['id'])->get();
        $this->debts = [];
        foreach ($saleDebts as $debt) {
            $this->debts[$debt['sale_id']] = $debt['paid'];
        }
        $this->oldDebts = [];
        $this->oldDebts = $this->debts;
        $this->getSales();
    }

    public function updateGift($id)
    {
        $gift = EmployeeGift::where('id', $id)->first();

        if ($gift['payment'] == 'cash') {
            \App\Models\Safe::first()->increment('currentBalance', $gift['gift_amount']);
        } else {
            Bank::where('id', $gift['bank_id'])->increment('currentBalance', $gift['gift_amount']);
        }

        $employee = \App\Models\Employee::find($this->currentEmployee['id']);
        $keys = array_keys($employee->sales->keyBy('id')->toArray());
        SaleDebt::where('due_date', $gift['gift_date'])->whereIn('sale_id', $keys)->where('paid', '!=', 0)->delete();

        $gift->update([
            'gift_amount' => $this->gift_amount,
            'gift_date' => $this->gift_date,
            'note' => $this->note
        ]);

        if (!empty($this->debts)) {
            foreach ($this->debts as $key => $debt) {
                SaleDebt::create([
                    'sale_id' => $key,
                    'paid' => $debt,
                    'bank' => '',
                    'payment' => 'cash',
                    'remainder' => 0,
                    'client_balance' => $gift['id'],
                    'due_date' => $this->gift_date
                ]);
            }
        }


        if ($this->payment == 'cash') {
            \App\Models\Safe::first()->decrement('currentBalance', $this->gift_amount);
        } else {
            Bank::where('id', $this->bank_id)->decrement('currentBalance', $this->gift_amount);
        }

        $this->getGifts($this->currentEmployee);
        $this->gift_date = date('Y-m-d');
        $this->reset('currentGift', 'editGiftMode', 'gift_amount', 'note', 'debts', 'oldDebts');
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
        $this->reset('id', 'employeeName', 'id', 'salary', 'editMode', 'currentEmployee', 'debts', 'total_debts','total_sum_paid');
    }

    public function render()
    {
        $this->employees = \App\Models\Employee::where('employeeName', 'like', '%' . $this->search . '%')->get();
        $this->banks = Bank::all();
        return view('livewire.employee');
    }
}
