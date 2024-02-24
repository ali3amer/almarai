<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\ClientDebt;
use App\Models\SaleDebt;
use App\Models\EmployeeGift;
use App\Models\SaleDetail;
use Illuminate\Database\Eloquent\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Employee extends Component
{
    use LivewireAlert;

    protected $listeners = [
        'delete',
        'deleteGift',
        'deleteDebt'
    ];
    public string $title = 'الموظفين';
    public int $id = 0;
    public $bank_id = null;
    public string $employeeName = '';
    public $salary = 0;
    public $paid = 0;
    public $remainder = 0;
    public $startingDate;
    public Collection $debts;
    public Collection $details;
    public string $search = '';
    public string $type = '';
    public string $gift_date = '';
    public $gift_id = null;
    public $sale_debt_id = null;
    public string $bank = '';
    public string $payment = 'cash';
    public string $note = '';
    public $gift_amount = 0;
    public $initialBalance = 0;

    public array $currentEmployee = [];
    public bool $editMode = false;
    public bool $editGiftMode = false;
    public bool $editDebtMode = false;
    public Collection $employees;
    public Collection $gifts;
    public Collection $banks;
    public Collection $sales;
    public array $currentDebt = [];
    public float $currentBalance = 0;
    public int $debtId = 0;
    public string $due_date = '';
    public $discount = 0;
    public bool $create = false;
    public bool $read = false;
    public bool $update = false;
    public bool $delete = false;
    public $month = "";

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
        $date = str_split(session("date"));
        $this->month = $date[5] . $date[6];

        $user = auth()->user();
        $this->create = $user->hasPermission('employees-create');
        $this->read = $user->hasPermission('employees-read');
        $this->update = $user->hasPermission('employees-update');
        $this->delete = $user->hasPermission('employees-delete');

        $this->banks = Bank::all();
        if ($this->banks->count() != 0) {
            $this->bank_id = $this->banks->first()->id;
        }

        $this->startingDate = session("date");


    }

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Employee::create(['employeeName' => $this->employeeName, 'salary' => $this->salary, "startingDate" => $this->startingDate]);
                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

            } else {
                $employee = \App\Models\Employee::find($id);
                $employee->employeeName = $this->employeeName;
                $employee->salary = $this->salary;
                $employee->startingDate = $this->startingDate;
                $employee->initialBalance = $this->initialBalance;
                $employee->save();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
            }
            $this->id = 0;
            $this->employeeName = '';
            $this->salary = 0;
            $this->resetData();
        }

    }

    public function edit($employee)
    {
        $this->editMode = true;
        $this->id = $employee['id'];
        $this->employeeName = $employee['employeeName'];
        $this->salary = $employee['salary'];
        $this->initialBalance = $employee['initialBalance'];
        $this->startingDate = $employee['startingDate'];
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
        $this->confirm("  هل توافق على الحذف؟  ", [
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

    public function deleteDebtMessage($id)
    {
        $this->confirm("  هل توافق على الحذف؟  ", [
            'inputAttributes' => ["id" => $id],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "deleteDebt",
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
        $this->gift_date = session("date");
        $this->gift_amount = $this->currentEmployee['salary'];
        $this->gifts = EmployeeGift::where('employee_id', $this->currentEmployee['id'])->get();
        $this->debts = SaleDebt::where('employee_id', $this->currentEmployee['id'])->get();
        $this->currentEmployee['gifts'] = EmployeeGift::where("employee_id", $this->currentEmployee["id"])->where("gift_date", "LIKE", date("Y") . "-%" . $this->month . "-%")->sum("gift_amount");
        $this->currentBalance = $this->debts->sum('debt') - $this->debts->sum('paid') - $this->debts->sum('discount') + $this->currentEmployee['initialBalance'];

        $this->calcRemainder();
    }

    public function chooseDebt($debt)
    {
        $this->currentDebt = $debt;
//        $this->editMode = true;
//        $this->debtId = $debt['id'];
//        $this->bank_id = $debt['bank_id'];
//        $this->type = $debt['type'];
//        $this->gift_amount = 0;
//        $this->paid = $debt['type'] == 'debt' ? $debt['debt'] : $debt['paid'];
//        $this->payment = $debt['payment'];
//        $this->bank = $debt['bank'];
//        $this->due_date = $debt['due_date'];
    }

    public function deleteDebt($data)
    {
        $id = $data['inputAttributes']['id'];
        SaleDebt::where('id', $id)->delete();
        $this->getGifts($this->currentEmployee);

        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
    }

    public function calcRemainder()
    {
        if ($this->gift_amount > 0) {
            $this->remainder = floatval($this->gift_amount) - floatval($this->paid);
        } else {
            $this->remainder = 0;
        }
    }

    public function payGift()
    {
        if (floatval($this->remainder) > floatval(session($this->payment == "cash" ? "safeBalance" : "bankBalance"))) {
            $this->confirm("المبلغ المدفوع أكبر من المبلغ المتوفر", [
                'toast' => false,
                'showConfirmButton' => false,
                'confirmButtonText' => 'موافق',
                'onConfirmed' => "cancelSale",
                'showCancelButton' => true,
                'cancelButtonText' => 'إلغاء',
                'confirmButtonColor' => '#dc2626',
                'cancelButtonColor' => '#4b5563'
            ]);
        } else {

            if ($this->paid != 0 || $this->discount != 0) {
                $type = 'pay';
                $note = $this->gift_amount == 0 ? 'تم إستلام مبلغ' : 'تم إستلام مبلغ من المرتب';
                $paid = $this->paid;
                $debt = 0;

               $debt = SaleDebt::create([
                    'Employee_id' => $this->currentEmployee['id'],
                    'type' => $type,
                    'debt' => $debt,
                    'paid' => $paid,
                    'discount' => floatval($this->discount),
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                    'bank' => $this->bank,
                    'due_date' => $this->gift_date,
                    'note' => $this->note == '' ? $note : $this->note,
                    'user_id' => auth()->id(),
                ]);

               $this->sale_debt_id = $debt->id;

            }

            if ($this->gift_amount != 0) {
                $gift = EmployeeGift::create([
                    'employee_id' => $this->currentEmployee['id'],
                    'sale_debt_id' => $this->sale_debt_id,
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                    'bank' => $this->bank,
                    'gift_amount' => $this->remainder,
                    'gift_date' => $this->gift_date,
                    'note' => $this->note
                ]);
            }


            $this->getGifts($this->currentEmployee);

            $this->alert('success', 'تم الدفع بنجاح', ['timerProgressBar' => true]);

            $this->resetData();

        }

    }

    public function editGift(EmployeeGift $gift)
    {
        dd($gift->debt);
        $this->editGiftMode = true;
        $this->currentDebt = $gift;
        $this->gift_id = $this->currentDebt['id'];
        $this->type = $this->currentDebt['type'];
        $this->bank_id = $this->currentDebt['bank_id'];
        $this->bank = $this->currentDebt['bank'];
        $this->paid = $this->currentDebt['paid'];
        $this->note = $this->currentDebt['note'];
        $this->gift_date = $this->currentDebt['gift_date'];
        $this->gift_amount = $salary + $this->currentDebt['paid'];
        $this->currentDebt['gift_amount'] = $salary;
        $this->remainder = $salary;

    }

    public function updateGift($id)
    {
        $type = 'pay';

        $note = $this->gift_amount == 0 ? 'تم إستلام مبلغ' : 'تم إستلام مبلغ من المرتب';
        $paid = $this->paid;
        $debt = 0;

        if ($this->gift_amount != 0) {
            $gift = EmployeeGift::where('id', $id)->update([
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'bank' => $this->bank,
                'gift_amount' => $this->gift_amount - $this->paid,
                'gift_date' => $this->gift_date,
                'note' => $this->note
            ]);

            SaleDebt::where('gift_id', $id)->update([
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
        } else {
            SaleDebt::where('id', $this->debtId)->update([
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
        }


        $this->resetData();

        $this->getGifts($this->currentEmployee);

        $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);

    }

    public function deleteGift($data)
    {
        $gift = EmployeeGift::where('id', $data['inputAttributes']['id'])->first();
        SaleDebt::where('id', $gift['sale_debt_id'])->delete();

        $gift->delete();
        $this->getGifts($this->currentEmployee);
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
    }

    #[On('reset-employee')]
    public function resetData($data = null)
    {
        $this->reset('id', 'employeeName', 'id', 'salary', 'editMode', 'currentDebt', 'sale_debt_id', 'note', 'paid', 'remainder', 'editGiftMode', 'initialBalance', $data);
    }

    public function render()
    {
        if ($this->payment == "bank" && $this->bank_id == null) {
            if ($this->banks->count() != 0) {
                $this->bank_id = $this->banks->first()->id;
            }
        }
        $this->employees = \App\Models\Employee::where('employeeName', 'like', '%' . $this->search . '%')->get();
        return view('livewire.employee');
    }
}
