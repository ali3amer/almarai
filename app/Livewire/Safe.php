<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Safe extends Component
{
    use LivewireAlert;
    protected $listeners = [
        'deleteTransfer',
    ];

    public string $title = 'الخزنه';

    public int $id = 0;
    public int $bank_id = 1;
    public int $transferId = 0;
    public string $bankName = '';
    public string $accountName = '';
    public $number = 0;
    public string $transfer_date = '';
    public string $note = '';
    public $transfer_number = '';
    public string $day_date = '';
    public $transfer_amount = 0;
    public $initialBalance = 0;
    public $currentBalance = 0;
    public $safe = 0;
    public $bank = 0;
    public string $transfer_type = 'cash_to_bank';

    public Collection $banks;
    public Collection $transfers;
    public Collection $clientDebts;
    public Collection $supplierDebts;
    public Collection $employeeDebts;
    public string $bankSearch = '';

    public function saveBank()
    {
        if ($this->id == 0) {
            Bank::create([
                'bankName' => $this->bankName,
                'accountName' => $this->accountName,
                'number' => intval($this->number),
                'initialBalance' => floatval($this->initialBalance),
                'currentBalance' => floatval($this->currentBalance),
            ]);
        } else {
            Bank::where('id', $this->id)->update([
                'bankName' => $this->bankName,
                'accountName' => $this->accountName,
                'number' => intval($this->number),
                'initialBalance' => floatval($this->initialBalance),
                'currentBalance' => floatval($this->currentBalance),
            ]);
        }
    }

    public function saveTransfer()
    {
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
            $transfer = Transfer::where('id', $this->transferId)->first();

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
    }

    public function showDayReport()
    {

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
            'inputAttributes' => ["transfer"=>$transfer],
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
        if ($transfer['transfer_type'] == 'cash_to_bank') {
            \App\Models\Safe::first()->increment('currentBalance', $transfer['transfer_amount']);
            Bank::where('id', $transfer['bank_id'])->first()->decrement('currentBalance', $transfer['transfer_amount']);
        } else {
            \App\Models\Safe::first()->decrement('currentBalance', $transfer['transfer_amount']);
            Bank::where('id', $transfer['bank_id'])->first()->increment('currentBalance', $transfer['transfer_amount']);
        }
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);

    }

    public function resetData()
    {
        $this->reset('transfer_type', 'transfer_number', 'bank_id', 'note', 'transfer_amount', 'transferId', 'transfer_date');
    }

    public function render()
    {

        if ($this->transfer_date == '') {
            $this->transfer_date = date('Y-m-d');
        }

        if ($this->day_date == '') {
            $this->day_date = date('Y-m-d');
        }

        $this->banks = Bank::where('bankName', 'LIKE', '%' . $this->bankSearch . '%')->get();
        $this->transfers = Transfer::with('bank')->get();
        return view('livewire.safe');
    }
}
