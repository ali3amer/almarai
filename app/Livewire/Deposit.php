<?php

namespace App\Livewire;

use App\Models\DepositDebt;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Bank;
use App\Models\DebtDetail;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Deposit extends Component
{
    use LivewireAlert;

    protected $listeners = [
        'delete',
        'deleteDebt'
    ];
    public string $title = 'العهد والأمانات';
    public int $id = 0;
    public int $debtId = 0;
    public string $name = '';
    public string $phone = '';
    public string $search = '';
    public string|null $note = '';
    public $initialSalesBalance = 0;
    public $initialPurchasesBalance = 0;
    public $initialDepositsBalance = 0;
    public $amount = 0;
    public string $bank = '';
    public string $startingDate = '';
    public Collection $banks;
    public null|int $bank_id = null;
    public Collection $deposits;
    public array $currentDeposit = [];
    public Collection $debts;
    public string $type = 'pay';
    public string $payment = 'cash';
    public string $due_date = '';
    public bool $blocked = false;
    public float $currentBalance = 0;
    public array $currentDebt = [];
    public bool $create = false;
    public bool $read = false;
    public bool $update = false;
    public bool $delete = false;
    public array $currentReceipt = [];

    protected function rules()
    {
        return [
            'name' => 'required|unique:people,name,' . $this->id
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'الرجاء إدخال الإسم',
            'name.unique' => 'هذا الشخص موجود مسبقاً'
        ];
    }

    public function mount()
    {
        $this->banks = Bank::all();
        if ($this->banks->count() != 0) {
            $this->bank_id = $this->banks->first()->id;
        }
        $user = auth()->user();
        $this->create = $user->hasPermission('deposits-create');
        $this->read = $user->hasPermission('deposits-read');
        $this->update = $user->hasPermission('deposits-update');
        $this->delete = $user->hasPermission('deposits-delete');
    }

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\People::create(['name' => $this->name, 'phone' => $this->phone, 'initialSalesBalance' => floatval($this->initialSalesBalance), 'initialDepositsBalance' => floatval($this->initialDepositsBalance), 'initialPurchasesBalance' => floatval($this->initialPurchasesBalance), 'type' => "deposit", 'startingDate' => $this->startingDate, 'blocked' => $this->blocked]);
                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
            } else {
                $deposit = \App\Models\People::find($id);
                $deposit->name = $this->name;
                $deposit->phone = $this->phone;
                $deposit->note = $this->note;
                $deposit->initialSalesBalance = floatval($this->initialSalesBalance);
                $deposit->initialPurchasesBalance = floatval($this->initialPurchasesBalance);
                $deposit->initialDepositsBalance = floatval($this->initialDepositsBalance);
                $deposit->blocked = $this->blocked;

                $deposit->save();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
            }
            $this->resetData();
        }

    }

    public function changeBlocked($deposit)
    {
        $this->blocked = !$deposit['blocked'];
        \App\Models\People::where('id', $deposit['id'])->update(['blocked' => $this->blocked]);
        $this->resetData();
        $this->alert('success', "تم تغيير حالة الى النقدي", ['timerProgressBar' => true]);

    }

    public function edit($deposit)
    {
        $this->id = $deposit['id'];
        $this->name = $deposit['name'];
        $this->phone = $deposit['phone'];
        $this->initialSalesBalance = $deposit['initialSalesBalance'];
        $this->initialPurchasesBalance = $deposit['initialPurchasesBalance'];
        $this->initialDepositsBalance = $deposit['initialDepositsBalance'];
        $this->blocked = $deposit['blocked'];
        $this->note = $deposit['note'];
        $this->startingDate = $deposit['startingDate'];

    }

    public function deleteMessage($deposit)
    {
        $this->confirm("  هل توافق على حذف الشخص  " . $deposit['name'] . "؟", [
            'inputAttributes' => ["id" => $deposit['id']],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "delete",
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }

    public function delete($data)
    {
        $deposit = \App\Models\Deposit::find($data['inputAttributes']['id']);
        $deposit->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
    }

    public function showDebts($deposit)
    {
        $this->currentDeposit = $deposit;
        $this->debts = DepositDebt::where("people_id", $this->currentDeposit['id'])->get();
        $this->currentBalance = \App\Models\People::find($this->currentDeposit['id'])->currentDepositsBalance;

    }

    public function saveDebt()
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
                        'people_id' => $this->currentDeposit['id'],
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

            $this->showDebts($this->currentDeposit);
            $this->showReceipt($debt->toArray());

        }

    }

    public function showReceipt($debt)
    {
        $this->currentReceipt = $debt;
    }

    public function chooseDebt($debt)
    {
        $this->currentDebt = $debt;
        $this->debtId = $debt['id'];
        $this->bank_id = $debt['bank_id'];
        $this->type = $debt['type'];
        $this->amount = $debt['amount'];
        $this->payment = $debt['payment'];
        $this->bank = $debt['bank'];
        $this->due_date = $debt['due_date'];
    }

    public function deleteDebtMessage($id)
    {
        $this->confirm("  هل توافق على الحذف؟", [
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

    public function deleteDebt($data)
    {
        $id = $data['inputAttributes']['id'];

        DepositDebt::where('id', $id)->forceDelete();
        $this->showDebts($this->currentDeposit);

        $this->alert('success', 'تم حذف الدفعيه بنجاح', ['timerProgressBar' => true]);

    }


    public function resetData($data = null)
    {
        $this->reset('type', 'name', 'initialSalesBalance', 'initialPurchasesBalance', 'initialDepositsBalance', 'amount', 'debtId', 'payment', 'bank', 'bank_id', 'due_date', 'blocked', 'note', $data);
    }

    public function render()
    {
        if ($this->payment == "bank" && $this->bank_id == null) {
            if ($this->banks->count() != 0) {
                $this->bank_id = $this->banks->first()->id;
            }
        }

        if ($this->due_date == '') {
            $this->due_date = session("date");
        }

        if ($this->startingDate == '') {
            $this->startingDate = session("date");
        }
        $this->deposits = \App\Models\People::where("type", "deposit")->where('name', 'like', '%' . $this->search . '%')->get();
        return view('livewire.deposit');
    }
}
