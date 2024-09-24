<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\ClientDebt;
use App\Models\DepositDebt;
use App\Models\PurchaseDebt;
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
    public bool $show = false;

    public int $id = 0;
    public $bank_id = null;
    public string $name = '';
    public $salary = 0;
    public $paid = 0;
    public $startingDate = '';
    public array $debts = [];
    public Collection $details;
    public string $search = '';
    public string $type = 'gift';
    public string $due_date = '';
    public $bank = '';
    public string $payment = 'cash';
    public string $processType = 'cash';
    public $note = null;
    public $amount = 0;
    public $initialSalesBalance = 0;
    public $initialPurchasesBalance = 0;
    public $initialDepositsBalance = 0;

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
    public $discount = 0;
    public bool $create = false;
    public bool $read = false;
    public bool $update = false;
    public bool $delete = false;
    public $month = "";
    public $gift_id = 0;
    public array $currentReceipt = [];
    public $debtType = 'sales';

    protected function rules()
    {
        return [
            'name' => 'required|unique:people,name,' . $this->id
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'الرجاء إدخال إسم الموظف',
            'name.unique' => 'هذا المورد موجود مسبقاً'
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
                \App\Models\People::create(['name' => $this->name, 'initialSalesBalance' => $this->initialSalesBalance, 'initialPurchasesBalance' => $this->initialPurchasesBalance, 'initialDepositsBalance' => $this->initialDepositsBalance, "startingDate" => $this->startingDate]);
                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

            } else {
                $employee = \App\Models\People::find($id);
                $employee->name = $this->name;
                $employee->startingDate = $this->startingDate;
                $employee->initialSalesBalance = $this->initialSalesBalance;
                $employee->initialPurchasesBalance = $this->initialPurchasesBalance;
                $employee->initialDepositsBalance = $this->initialDepositsBalance;
                $employee->save();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
            }

            $this->resetData();
        }

    }

    public function edit($employee)
    {
        $this->editMode = true;
        $this->id = $employee['id'];
        $this->name = $employee['name'];
        $this->initialSalesBalance = $employee['initialSalesBalance'];
        $this->initialPurchasesBalance = $employee['initialPurchasesBalance'];
        $this->initialDepositsBalance = $employee['initialDepositsBalance'];
        $this->startingDate = $employee['startingDate'];
    }

    public function deleteMessage($employee)
    {
        $this->confirm("  هل توافق على حذف الموظف  " . $employee['name'] . "؟", [
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
        $employee = \App\Models\People::find($data['inputAttributes']['id']);
        $employee->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);

    }

    public function getGifts($employee = null)
    {
        if ($employee == null) {
            $employee = $this->currentEmployee;
        }

        $this->currentEmployee = $employee;

        if ($this->debtType == "sales") {
            $this->currentBalance = \App\Models\People::find($this->currentEmployee['id'])->currentSalesBalance;
            $this->debts = (new \App\Models\Sale)->getMovements($this->currentEmployee['id'])->where("due_date", session("date"))->toArray();
            $this->type = "pay";

        } elseif ($this->debtType == "purchases") {
            $this->currentBalance = \App\Models\People::find($this->currentEmployee['id'])->currentPurchasesBalance;
            $this->debts = (new \App\Models\Purchase)->getMovements($this->currentEmployee['id'])->where("due_date", session("date"))->toArray();
            $this->type = "pay";

        } elseif ($this->debtType == 'deposits') {
            $this->debts = (new \App\Models\Deposit)->getMovements($this->currentEmployee['id'])->where("due_date", session("date"))->toArray();
            $this->currentBalance = \App\Models\People::find($this->currentEmployee['id'])->currentDepositsBalance;
            $this->type = "pay";

        }

        $this->due_date = session("date");
        $this->gifts = EmployeeGift::where('people_id', $this->currentEmployee['id'])->get();
        $this->currentEmployee['gifts'] = EmployeeGift::where("people_id", $this->currentEmployee["id"])->where("due_date", "LIKE", date("Y") . "-%" . $this->month . "-%")->sum("amount");

    }

    public function payGift()
    {
        if (floatval($this->amount) > floatval(session($this->payment == "cash" ? "safeBalance" : "bankBalance"))) {
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

            if ($this->gift_id == 0) {
                EmployeeGift::create([
                    'people_id' => $this->currentEmployee['id'],
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                    'bank' => $this->bank,
                    'amount' => $this->amount,
                    'due_date' => $this->due_date,
                    'note' => $this->note ?? "تم دفع مبلغ للموظف"
                ]);

                $this->alert('success', 'تم الدفع بنجاح', ['timerProgressBar' => true]);

            } else {
                EmployeeGift::where('id', $this->gift_id)->update([
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                    'bank' => $this->bank,
                    'amount' => $this->amount,
                    'due_date' => $this->due_date,
                    'note' => $this->note ?? "تم دفع مبلغ للموظف"
                ]);
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
            }

            $this->getGifts($this->currentEmployee);

            $this->resetData();

        }
    }

    public function editGift(EmployeeGift $gift)
    {
        $this->editGiftMode = true;
        $this->gift_id = $gift->id;
        $this->type = "gift";
        $this->payment = $gift->payment;
        $this->bank_id = $gift->bank_id;
        $this->bank = $gift->bank;
        $this->amount = $gift->amount;
        $this->note = $gift->note;
        $this->due_date = $gift->due_date;

    }

    public function deleteGift($data)
    {
        $gift = EmployeeGift::where('id', $data['inputAttributes']['id'])->first();

        $gift->delete();
        $this->getGifts($this->currentEmployee);
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
    }

    public function saveDebt()
    {
        if ($this->debtType == "deposits") {
            $this->saveDepositDebt();
        } elseif ($this->debtType == "sales") {
            $this->saveSaleDebt();
        } elseif ($this->debtType == "purchases") {
            $this->savePurchaseDebt();
        }

    }

    public function saveSaleDebt()
    {

        if ($this->type == 'debt') {
            $note = 'تم إستلاف مبلغ';
        } elseif ($this->type == "discount") {
            $note = 'تم خصم مبلغ';
        } else {
            $note = 'تم إستلام مبلغ';
        }

        if ($this->type == "debt" && floatval($this->amount) > floatval(session($this->payment == "cash" ? "safeBalance" : "bankBalance"))) {
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
            if ($this->debtId == 0) {

                if (floatval($this->amount) != 0) {
                    $debt = SaleDebt::create([
                        'people_id' => $this->currentEmployee['id'],
                        'type' => $this->type,
                        'amount' => $this->amount,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'bank' => $this->bank,
                        'due_date' => $this->due_date,
                        'note' => $this->note == '' ? $note : $this->note,
                        'user_id' => auth()->id(),
                    ]);
                }

                $this->alert('success', 'تم السداد بنجاح', ['timerProgressBar' => true]);
            } else {
                $debt = SaleDebt::where('id', $this->debtId)->first();

                $debt->type = $this->type;
                $debt->amount = $this->amount;
                $debt->payment = $this->payment;
                $debt->bank_id = $this->payment == 'bank' ? $this->bank_id : null;
                $debt->bank = $this->bank;
                $debt->due_date = $this->due_date;
                $debt->user_id = auth()->id();
                $debt->save();
                $this->alert('success', 'تم تعديل الدفعيه بنجاح', ['timerProgressBar' => true]);

            }
            $this->showReceipt($debt->toArray());

        }
        $this->resetData();
        $this->getGifts($this->currentEmployee);

    }

    public function savePurchaseDebt()
    {
        if ($this->type == 'debt') {
            $note = 'تم إستلاف مبلغ';
        } elseif ($this->type == "discount") {
            $note = 'تم خصم مبلغ';
        } else {
            $note = 'تم دفع مبلغ';
        }

        if ($this->type == "pay" && floatval($this->amount) > floatval(session($this->payment == "cash" ? "safeBalance" : "bankBalance"))) {
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
            if ($this->debtId == 0) {

                if (floatval($this->amount) != 0) {
                    $debt = PurchaseDebt::create([
                        'people_id' => $this->currentEmployee['id'],
                        'type' => $this->type,
                        'amount' => $this->amount,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'bank' => $this->bank,
                        'due_date' => $this->due_date,
                        'note' => $this->note == '' ? $note : $this->note,
                        'user_id' => auth()->id(),
                    ]);
                }

                $this->alert('success', 'تم السداد بنجاح', ['timerProgressBar' => true]);

            } else {

                $debt = PurchaseDebt::where('id', $this->debtId)->first();

                $debt->type = $this->type;
                $debt->amount = $this->amount;
                $debt->payment = $this->payment;
                $debt->bank_id = $this->payment == 'bank' ? $this->bank_id : null;
                $debt->bank = $this->bank;
                $debt->due_date = $this->due_date;
                $debt->user_id = auth()->id();
                $debt->save();

                $this->alert('success', 'تم تعديل الدفعيه بنجاح', ['timerProgressBar' => true]);

            }
            $this->showReceipt($debt->toArray());
        }

        $this->resetData();
        $this->getGifts($this->currentEmployee);

    }

    public function saveDepositDebt()
    {
        if ($this->type == "debt" && floatval($this->amount) > floatval(session($this->payment == "cash" ? "safeBalance" : "bankBalance"))) {
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
            if ($this->debtId == 0) {
                if ($this->type == 'pay') {
                    $note = 'تم إيداع مبلغ';
                } else {
                    $note = 'تم سحب مبلغ';
                }
                if (floatval($this->amount) != 0) {
                    $debt = DepositDebt::create([
                        'people_id' => $this->currentEmployee['id'],
                        'type' => $this->type,
                        'amount' => $this->amount,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'bank' => $this->bank,
                        'due_date' => $this->due_date,
                        'note' => $this->note == '' ? $note : $this->note,
                        'user_id' => auth()->id(),
                    ]);
                }

                $this->alert('success', $note, ['timerProgressBar' => true]);

            } else {
                $debt = DepositDebt::where('id', $this->debtId)->first();

                $debt->type = $this->type;
                $debt->amount = $this->amount;
                $debt->payment = $this->payment;
                $debt->bank_id = $this->payment == 'bank' ? $this->bank_id : null;
                $debt->bank = $this->bank;
                $debt->due_date = $this->due_date;
                $debt->user_id = auth()->id();

                $debt->save();

                $this->alert('success', 'تم تعديل الدفعيه بنجاح', ['timerProgressBar' => true]);

            }
            $this->resetData();

            $this->getGifts($this->currentEmployee);
            $this->showReceipt($debt->toArray());

        }

    }

    public function showReceipt($debt)
    {
        $this->currentReceipt = (array)$debt;
    }

    public function chooseDebt($debt)
    {
        $this->editDebtMode = true;
        $this->currentDebt = $debt;
        $this->debtId = $debt['id'];
        $this->bank_id = $debt['bank_id'];
        $this->type = $debt['type'];
        $this->note = $debt['note'];
        $this->amount = $debt['amount'] ?? $debt['income'];
        $this->payment = $debt['payment'];
        $this->bank = $debt['bank'];
        $this->due_date = $debt['due_date'];
    }


    public function deleteDebt($data)
    {
        $id = $data['inputAttributes']['id'];

        if ($this->debtType == 'purchases') {
            PurchaseDebt::where('id', $id)->forceDelete();
        } elseif ($this->debtType == 'sales') {
            SaleDebt::where('id', $id)->forceDelete();
        } elseif ($this->debtType == 'deposits') {
            DepositDebt::where('id', $id)->forceDelete();
        }

        $this->getGifts($this->currentEmployee);

        $this->resetData();

        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
    }

    #[On('reset-employee')]
    public function resetData($data = null)
    {
        $this->reset('id', 'name', 'gift_id', "type", 'debtId', 'editMode', 'currentDebt', 'payment', 'bank', 'bank_id', 'note', 'amount', 'editGiftMode', 'editDebtMode', 'initialSalesBalance', 'initialPurchasesBalance', 'initialDepositsBalance', 'discount', $data);
    }

    public function render()
    {
        if ($this->payment == "bank" && $this->bank_id == null) {
            if ($this->banks->count() != 0) {
                $this->bank_id = $this->banks->first()->id;
            }
        }

        if (empty($this->currentEmployee)) {
            $this->employees = \App\Models\People::where("type", "employee")->where('name', 'like', '%' . $this->search . '%')->get();
        }

        return view('livewire.employee');
    }
}
