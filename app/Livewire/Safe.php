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
    public int $transferId = 0;
    public string $bankName = '';
    public string $accountName = '';
    public $number = 0;
    public string $transfer_date = '';
    public $transfer_number = 0;
    public $transfer_amount = 0;
    public $initialBalance = 0;
    public $currentBalance = 0;
    public $safe = 0;
    public $bank = 0;
    public string $transfer_type = 'cash_to_bank';

    public Collection $banks;
    public Collection $transfers;
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
                'transfer_type' => $this->transfer_type,
                'transfer_amount' => $this->transfer_amount,
                'transfer_number' => $this->transfer_number,
                'transfer_date' => $this->transfer_date,
            ]);
        } else {
            Transfer::where('id', $this->transferId)->update([
                'transfer_type' => $this->transfer_type,
                'transfer_amount' => $this->transfer_amount,
                'transfer_number' => $this->transfer_number,
                'transfer_date' => $this->transfer_date,
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
    }

    public function deleteTransfer($transfer)
    {
        Transfer::where('id', $transfer['id'])->delete();
    }

    public function render()
    {
        if ($this->transfer_date == '') {
            $this->transfer_date = date('Y-m-d');
        }
        $this->safe = \App\Models\Safe::sum('currentBalance');
        $this->bank = \App\Models\Bank::sum('currentBalance');
        $this->banks = Bank::where('bankName', 'LIKE', '%' . $this->bankSearch . '%')->get();
        $this->transfers = Transfer::all();
        return view('livewire.safe');
    }
}
