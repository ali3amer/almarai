<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\ClientDebt;
use App\Models\EmployeeDebt;
use App\Models\EmployeeGift;
use App\Models\SaleDebt;
use App\Models\SaleDetail;
use Illuminate\Database\Eloquent\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Employee extends Component
{
    use LivewireAlert;
    protected $listeners = [
        'delete',
        'deleteGift'
    ];
    public string $title = 'الموظفين';
    public int $id = 0;
    public int|null $bank_id = 0;
    public string $employeeName = '';
    public $salary = 0;
    public $paid = 0;
    public $remainder = 0;
    public Collection $debts;
    public array $oldDebts = [];
    public Collection $details;
    public string $search = '';
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
    public float $safeBalance = 0;
    public float $bankBalance = 0;

    protected function rules()
    {
        return [
            'employeeName' => 'required|unique:employees,employeeName,' . $this->id
        ];
    }

    protected function messages()
    {
        return [
            'employeeName.required' => 'الرجاء إدخال إسم الموظف',
            'employeeName.unique' => 'هذا المورد موجود مسبقاً'
        ];
    }

    public function mount()
    {
        $this->safeBalance = \App\Models\Safe::first()->currentBalance;
        $this->bankBalance = \App\Models\Bank::first()->currentBalance;
    }

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Employee::create(['employeeName' => $this->employeeName, 'salary' => $this->salary]);
                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

            } else {
                $employee = \App\Models\Employee::find($id);
                $employee->employeeName = $this->employeeName;
                $employee->salary = $this->salary;
                $employee->save();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
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

    public function deleteMessage($employee)
    {
        $this->confirm("  هل توافق على حذف الموظف  " . $employee['employeeName'] . "؟", [
            'inputAttributes' => ["id" => $employee['id']],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "delete",
            "value" => $employee['id'],
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }

    public function deleteGiftMessage($gift)
    {
        $this->confirm("  هل توافق على الحذف؟  " , [
            'inputAttributes' => ["id" => $gift['id']],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "deleteGift",
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }

    public function delete($data)
    {
        $employee = \App\Models\Employee::find($data['inputAttributes']['id']);
        $employee->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);

    }

    public function getGifts($employee)
    {
        $this->currentEmployee = $employee;
        $this->gift_date = date('Y-m-d');
        $this->gift_amount = $this->currentEmployee['salary'];
        $this->gifts = EmployeeGift::where('employee_id', $this->currentEmployee['id'])->get();
        $this->debts = EmployeeDebt::where('employee_id', $this->currentEmployee['id'])->get();
        $this->getSales();
    }

    public function chooseDebt($debt)
    {
        $this->debtId = $debt['id'];
        $this->bank_id = $debt['bank_id'];
        $this->type = $debt['type'];
        $this->debt_amount = $debt['type'] == 'debt' ? $debt['debt'] : $debt['paid'];
        $this->payment = $debt['payment'];
        $this->bank = $debt['bank'];
        $this->due_date = $debt['due_date'];
    }

    public function getSales()
    {
        $this->sales = \App\Models\Sale::where('employee_id', $this->currentEmployee['id'])->orderBy('id', 'desc')->get()->keyBy('id');
    }

    public function addDebt($sale)
    {
        $remainder = 'remainder';
        if (end($sale['sale_debts'])[$remainder] != 0 && !key_exists($sale['id'], $this->debts) && $this->gift_amount > end($sale['sale_debts'])[$remainder]) {
            $this->debts[$sale['id']] = end($sale['sale_debts'])[$remainder];
            $this->gift_amount -= end($sale['sale_debts'])[$remainder];
            $this->calcDebts();
        }
        $remainder = 'paid';
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
        $this->remainder = floatval($this->gift_amount) - floatval($this->paid);
    }

    public function showSale($sale)
    {
        $this->details = SaleDetail::where('sale_id', $sale['id'])->get();
    }

    public function payGift()
    {
            $gift = EmployeeGift::create([
                'employee_id' => $this->currentEmployee['id'],
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'gift_amount' => $this->gift_amount - $this->paid,
                'gift_date' => $this->gift_date,
                'note' => $this->note
            ]);
            $type = 'pay';
        if ($type == 'debt') {
            $note = 'تم شراء بالآجل';
            $debt = $this->gift_amount;
            $paid = 0;
        } else {
            $note = 'تم إستلام مبلغ من المرتب';
            $paid = $this->paid;
            $debt = 0;
        }
        EmployeeDebt::create([
            'Employee_id' => $this->currentEmployee['id'],
            'gift_id' => $gift['id'],
            'type' => $type,
            'debt' => $debt,
            'paid' => $paid,
            'payment' => $this->payment,
            'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
            'bank' => $this->bank,
            'due_date' => $this->gift_date,
            'note' => $this->note == '' ? $note : $this->note,
            'user_id' => auth()->id(),
        ]);


        if ($this->payment == 'cash') {
            \App\Models\Safe::first()->decrement('currentBalance', $this->gift_amount);
        } else {
            Bank::where('id', $this->bank_id)->decrement('currentBalance', $this->gift_amount);
        }

        $this->getGifts($this->currentEmployee);

        $this->alert('success', 'تم الدفع بنجاح', ['timerProgressBar' => true]);

    }

    public function editGift($gift)
    {
        $this->editGiftMode = true;
        dd(EmployeeGift::with('debt')->first()->debt);
        $this->currentGift = $gift;
        $this->gift_date = $this->currentGift['gift_date'];
        $this->gift_amount = $this->currentGift['gift_amount'];
        $this->note = $this->currentGift['note'];

        $saleDebts = SaleDebt::where('gift_id', $this->currentGift['id'])->get();
        $this->debts;
        foreach ($saleDebts as $debt) {
            if ($debt->paid != 0) {
                $this->debts[$debt['sale_id']] = $debt['paid'];
            }
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
                    'client_balance' => 0,
                    'gift_id' => $gift['id'],
                    'due_date' => $this->gift_date,
                    'user_id' => auth()->id()
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
        $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);

    }

    public function deleteGift($data)
    {
        $gift = EmployeeGift::where('id', $data['inputAttributes']['id'])->first();
        if ($gift['payment'] == 'cash') {
            \App\Models\Safe::first()->increment('currentBalance', $gift['gift_amount']);
        } else {
            Bank::where('id', $gift['bank_id'])->increment('currentBalance', $gift['gift_amount']);
        }
        $gift->delete();
        $this->getGifts($this->currentEmployee);
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
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
