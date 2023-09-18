<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Safe extends Component
{
    public string $title = 'الخزنه';

    public int $id = 0;
    public int $bank_id = 1;
    public int $transferId = 0;
    public string $bankName = '';
    public string $accountName = '';
    public $number = 0;
    public string $transfer_date = '';
    public string $note = '';
    public $transfer_number = 0;
    public $transfer_amount = 0;
    public $initialBalance = 0;
    public $currentBalance = 0;
    public $safe = 0;
    public $bank = 0;
    public string $transfer_type = 'cash_to_bank';

    public Collection $banks;
    public Collection $transfers;
    public Collection $user;
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
            if ($this->transfer_type == 'cash_to_bank') {
                Bank::where('id', $this->bank_id)->increment('currentBalance', $this->transfer_amount);
                \App\Models\Safe::first()->decrement('currentBalance', $this->transfer_amount);
            } elseif ($this->transfer_type == 'bank_to_cash') {
                Bank::where('id', $this->bank_id)->decrement('currentBalance', $this->transfer_amount);
                \App\Models\Safe::first()->increment('currentBalance', $this->transfer_amount);
            }
            session()->flash('success', 'تم الحفظ بنجاح');
        } else {
            $transfer = Transfer::where('id', $this->transferId)->first();
            if ($transfer['cash_to_bank'] == 'cash_to_bank') {
                Bank::where('id', $transfer['bank_id'])->decrement('currentBalance', $transfer['transfer_amount']);
                \App\Models\Safe::first()->increment('currentBalance', $transfer['transfer_amount']);
            } else {
                Bank::where('id', $transfer['bank_id'])->increment('currentBalance', $transfer['transfer_amount']);
                \App\Models\Safe::first()->decrement('currentBalance', $transfer['transfer_amount']);
            }

            Transfer::where('id', $this->transferId)->update([
                'bank_id' => $this->bank_id,
                'transfer_type' => $this->transfer_type,
                'transfer_amount' => $this->transfer_amount,
                'transfer_number' => $this->transfer_number,
                'transfer_date' => $this->transfer_date,
                'note' => $this->note,
            ]);
            session()->flash('success', 'تم التعديل بنجاح');

        }
        $this->resetData();
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

    public function deleteTransfer($transfer)
    {
        Transfer::where('id', $transfer['id'])->delete();
        if ($transfer['transfer_type'] == 'cash_to_bank') {
            \App\Models\Safe::first()->decrement('currentBalance', $transfer['transfer_amount']);
            Bank::where('id', $transfer['bank_id'])->first()->increment('currentBalance', $transfer['transfer_amount']);
        } else {
            \App\Models\Safe::first()->increment('currentBalance', $transfer['transfer_amount']);
            Bank::where('id', $transfer['bank_id'])->first()->decrement('currentBalance', $transfer['transfer_amount']);
        }
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
        $this->safe = \App\Models\Safe::sum('currentBalance');
        $this->bank = Bank::sum('currentBalance');
        $this->banks = Bank::where('bankName', 'LIKE', '%' . $this->bankSearch . '%')->get();
        $this->transfers = Transfer::with('bank')->get();
        return view('livewire.safe');
    }
}
