<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\Day;
use App\Models\EmployeeGift;
use App\Models\Expense;
use App\Models\PurchaseDebt;
use App\Models\SaleDebt;
use App\Models\Transfer;
use App\Models\Withdraw;
use Illuminate\Database\Eloquent\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Safe extends Component
{
    use LivewireAlert;

    protected $listeners = [
        'deleteTransfer',
        'deleteWithdraw'
    ];

    public string $title = 'الخزنه';

    public $startingDate;
    public $capital = 0;
    public int $id = 0;
    public $bank_id = null;
    public int $withdrawId = 0;
    public int $transferId = 0;
    public string $bankName = '';
    public string $accountName = '';
    public $number = 0;
    public $amount = 0;
    public string $transfer_date = '';
    public string $note = '';
    public $transfer_number = '';
    public string $day_date = '';
    public $transfer_amount = 0;
    public $initialBalance = 0;
    public $currentBalance = 0;
    public $safeBalance = 0;
    public $banksBalance = 0;
    public string $transfer_type = 'cash_to_bank';

    public Collection $days;
    public Collection $banks;
    public Collection $withdraws;
    public Collection $clientDebts;
    public Collection $supplierDebts;
    public Collection $employeeDebts;
    public string $bankSearch = '';
    public $safe = 0;
    public $safeId = 0;
    public $payment = "cash";
    public bool $create = false;
    public bool $read = false;
    public bool $update = false;
    public bool $delete = false;

    public function mount()
    {
        $user = auth()->user();
        $this->create = $user->hasPermission('employees-create');
        $this->read = $user->hasPermission('employees-read');
        $this->update = $user->hasPermission('employees-update');
        $this->delete = $user->hasPermission('employees-delete');

        $this->startingDate = session("date");
        $this->withdraws = Withdraw::all();
        $this->days = Day::all();
        $this->getbanksBalance();
    }

    public function changeStatus($day)
    {
        Day::where("id", $day['id'])->update([
            "closed" => !$day['closed'],
            "balance" => session("safeBalance")
        ]);
        $this->days = Day::all();
    }

    public function getbanksBalance()
    {
        $this->banks = Bank::all();

        if ($this->banks->count() > 0) {
            $this->bank_id = $this->banks->first()->id;
        }
    }

    public function getWithdraws()
    {
        $this->reset("payment", "amount", "withdrawId");
        $this->withdraws = Withdraw::all();
    }

    public function withdraw()
    {
        if ($this->withdrawId == 0) {
            Withdraw::create([
                "due_date" => session("date"),
                "user_id" => auth()->user()->id,
                "payment" => $this->payment,
                "bank_id" => $this->payment == "cash" ? null : $this->bank_id,
                "amount" => $this->amount,
            ]);
        } else {
            Withdraw::where("id", $this->withdrawId)->update([
                "user_id" => auth()->user()->id,
                "payment" => $this->payment,
                "bank_id" => $this->payment == "cash" ? null : $this->bank_id,
                "amount" => $this->amount,
            ]);
        }

        $this->alert('success', "تم إضافة مبلغ " . number_format($this->amount, 2) . " الى اليومية", ['timerProgressBar' => true]);
        $this->getWithdraws();

    }

    public function saveBank()
    {
        if ($this->id == 0) {
            Bank::create([
                'bankName' => $this->bankName,
                'accountName' => $this->accountName,
                'startingDate' => $this->startingDate,
                'number' => intval($this->number),
                'initialBalance' => floatval($this->initialBalance),
            ]);
        } else {
            Bank::where('id', $this->id)->update([
                'bankName' => $this->bankName,
                'accountName' => $this->accountName,
                'startingDate' => $this->startingDate,
                'number' => intval($this->number),
                'initialBalance' => floatval($this->initialBalance),
            ]);
        }

        $this->getbanksBalance();

        $this->resetBankData();
        $this->alert('success', 'تم حفظ بنجاح', ['timerProgressBar' => true]);

    }

    public function safeInitial()
    {
        if ($this->safeId == 0) {
            \App\Models\Safe::create(['initialBalance' => floatval($this->safe), "startingDate" => $this->startingDate, 'capital' => floatval($this->capital)]);
            $this->alert('success', 'تم حفظ الرصيد بنجاح', ['timerProgressBar' => true]);

        } else {
            \App\Models\Safe::where('id', $this->safeId)->update(['initialBalance' => floatval($this->safe), "startingDate" => $this->startingDate, 'capital' => floatval($this->capital)]);
            $this->alert('success', 'تم تعديل الرصيد بنجاح', ['timerProgressBar' => true]);
        }
        $this->safeId = 0;
    }

    public function saveTransfer()
    {
        if (floatval($this->transfer_amount) <= floatval(session($this->transfer_type == "cash_to_bank" ? "safeBalance" : "bankBalance"))) {
            if ($this->transferId == 0) {
                Transfer::create([
                    'bank_id' => $this->bank_id,
                    'transfer_type' => $this->transfer_type,
                    'transfer_amount' => $this->transfer_amount,
                    'transfer_number' => $this->transfer_number,
                    'transfer_date' => $this->transfer_date,
                    'note' => $this->note,
                ]);

                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
            } else {

                Transfer::where('id', $this->transferId)->update([
                    'bank_id' => $this->bank_id,
                    'transfer_type' => $this->transfer_type,
                    'transfer_amount' => $this->transfer_amount,
                    'transfer_number' => $this->transfer_number,
                    'transfer_date' => $this->transfer_date,
                    'note' => $this->note,
                ]);

                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);

            }
            $this->resetData();

        } else {

            $this->confirm("المبلغ المحول أكبر من المبلغ المتوفر", [
                'toast' => false,
                'showConfirmButton' => false,
                'confirmButtonText' => 'موافق',
                'onConfirmed' => "cancelSale",
                'showCancelButton' => true,
                'cancelButtonText' => 'إلغاء',
                'confirmButtonColor' => '#dc2626',
                'cancelButtonColor' => '#4b5563'
            ]);
        }


    }

    public function editTransfer($transfer)
    {
        $this->transferId = $transfer['id'];
        $this->transfer_type = $transfer['transfer_type'];
        $this->transfer_amount = $transfer['transfer_amount'];
        $this->transfer_number = $transfer['transfer_number'];
        $this->transfer_date = $transfer['transfer_date'];
        $this->note = $transfer['note'];
    }

    public function deleteMessage($transfer)
    {
        $this->confirm("  هل توافق على الحذف ؟", [
            'inputAttributes' => ["transfer" => $transfer],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "deleteTransfer",
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }

    public function deleteTransfer($data)
    {
        $transfer = $data['inputAttributes']['transfer'];
        Transfer::where('id', $transfer['id'])->delete();

        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);

    }

    public function editWithdraw($withdraw)
    {
        $this->withdrawId = $withdraw['id'];
        $this->amount = $withdraw['amount'];
    }

    public function deleteMessageWithdraw($withdraw)
    {
        $this->confirm("  هل توافق على الحذف ؟", [
            'inputAttributes' => ["withdraw" => $withdraw],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "deleteWithdraw",
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }

    public function deleteWithdraw($data)
    {
        $withdraw = $data['inputAttributes']['withdraw'];
        Withdraw::where('id', $withdraw['id'])->delete();
        $this->getWithdraws();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);

    }

    public function resetData()
    {
        $this->reset('transfer_type', 'transfer_number', 'note', 'transfer_amount', 'transferId', 'transfer_date');
    }

    public function resetBankData()
    {
        $this->reset('bankName', 'accountName', 'initialBalance', 'number');
    }

    public function render()
    {
        $this->safeBalance = \App\Models\Safe::first()->pastBalance;

        if ($this->transfer_date == '') {
            $this->transfer_date = session("date");
        }

        if ($this->day_date == '') {
            $this->day_date = session("date");
        }

        $this->days = Day::all();
        $this->getbanksBalance();
        return view('livewire.safe', [
            "transfers" => Transfer::all()
        ]);
    }
}
