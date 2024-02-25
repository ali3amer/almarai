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
    public $startingDate;
    public Collection $debts;
    public Collection $details;
    public string $search = '';
    public string $type = '';
    public string $gift_date = '';
    public $bank = '';
    public string $payment = 'cash';
    public string $processType = 'cash';
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
    public float $currentBalance = 0;
    public int $debtId = 0;
    public string $due_date = '';
    public $discount = 0;
    public bool $create = false;
    public bool $read = false;
    public bool $update = false;
    public bool $delete = false;
    public $month = "";
    public $gift_id = 0;

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

    }

    public function payGift()
    {
        if (floatval($this->gift_amount) > floatval(session($this->payment == "cash" ? "safeBalance" : "bankBalance"))) {
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
            EmployeeGift::create([
                'employee_id' => $this->currentEmployee['id'],
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'bank' => $this->bank,
                'gift_amount' => $this->gift_amount,
                'gift_date' => $this->gift_date,
                'note' => $this->note ?? "تم دفع مبلغ للموظف"
            ]);
        }

        $this->getGifts($this->currentEmployee);

        $this->resetData();


        $this->alert('success', 'تم الدفع بنجاح', ['timerProgressBar' => true]);

    }

    public function editGift(EmployeeGift $gift)
    {
        $this->editGiftMode = true;
        $this->gift_id = $gift->id;
        $this->type = "gift";
        $this->payment = $gift->payment;
        $this->bank_id = $gift->bank_id;
        $this->bank = $gift->bank;
        $this->gift_amount = $gift->gift_amount;
        $this->note = $gift->note;
        $this->gift_date = $gift->gift_date;

    }

    public function updateGift()
    {

        EmployeeGift::where('id', $this->gift_id)->update([
            'payment' => $this->payment,
            'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
            'bank' => $this->bank,
            'gift_amount' => $this->gift_amount,
            'gift_date' => $this->gift_date,
            'note' => $this->note ?? "تم دفع مبلغ للموظف"
        ]);

        $this->getGifts($this->currentEmployee);

        $this->resetData();

        $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);

    }

    public function deleteGift($data)
    {
        $gift = EmployeeGift::where('id', $data['inputAttributes']['id'])->first();

        $gift->delete();
        $this->getGifts($this->currentEmployee);
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
    }


    public function payDebt()
    {

        $type = 'pay';
        $note = 'تم إستلام مبلغ';
        $paid = floatval($this->gift_amount);
        $debt = 0;

        SaleDebt::create([
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

        if (floatval($this->discount) > 0) {
            SaleDebt::create([
                'Employee_id' => $this->currentEmployee['id'],
                'type' => "pay",
                'debt' => 0,
                'paid' => 0,
                'discount' => floatval($this->discount),
                'payment' => "cash",
                'due_date' => $this->gift_date,
                'note' => $this->note == '' ? $note : $this->note,
                'user_id' => auth()->id(),
            ]);
        }

        $this->getGifts($this->currentEmployee);

        $this->alert('success', 'تم الدفع بنجاح', ['timerProgressBar' => true]);

        $this->resetData();

    }

    public function editDebt($debt)
    {
        $this->editDebtMode = true;
        $this->debtId = $debt['id'];
        $this->type = "pay";
        $this->gift_amount = $debt['discount'] > 0 ? 0 : $debt['paid'];
        $this->discount = $debt['discount'] > 0 ? $debt['discount'] : 0;
        $this->payment = $debt['payment'];
        $this->bank = $debt['bank'];
        $this->bank_id = $debt['bank_id'];
        $this->gift_date = $debt['due_date'];
    }

    public function updateDebt()
    {
        SaleDebt::where('id', $this->debtId)->update([
            'type' => "pay",
            'debt' => 0,
            'paid' => floatval($this->gift_amount),
            'discount' => floatval($this->discount),
            'payment' => $this->payment,
            'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
            'bank' => $this->bank,
            'due_date' => $this->gift_date,
            'note' => $this->note,
            'user_id' => auth()->id(),
        ]);

        $this->getGifts($this->currentEmployee);

        $this->resetData();

        $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);

    }

    public function deleteDebt($data)
    {
        $id = $data['inputAttributes']['id'];
        SaleDebt::where('id', $id)->delete();
        $this->getGifts($this->currentEmployee);

        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
    }

    #[On('reset-employee')]
    public function resetData($data = null)
    {
        $this->reset('id', 'employeeName', 'gift_id', 'debtId', 'editMode', 'currentDebt', 'note', 'editGiftMode', 'editDebtMode', 'initialBalance', $data);
    }

    public function render()
    {
        if ($this->payment == "bank" && $this->bank_id == null) {
            if ($this->banks->count() != 0) {
                $this->bank_id = $this->banks->first()->id;
            }
        }

        if (empty($this->currentEmployee)) {
            $this->employees = \App\Models\Employee::where('employeeName', 'like', '%' . $this->search . '%')->get();
        }

        return view('livewire.employee');
    }
}
