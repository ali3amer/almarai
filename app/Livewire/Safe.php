<?php

namespace App\Livewire;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Safe extends Component
{
    public string $title = 'الخزنه';

    public int $id = 0;
    public string $bankName = '';
    public string $accountName = '';
    public int $number = 0;
    public float $initialBalance = 0;
    public float $currentBalance = 0;
    public float $safe = 0;
    public float $bank = 0;

    public Collection $banks;
    public string $bankSearch ='';

    public function saveBank() {
        if ($this->id == 0) {
            Bank::create([
                'bankName' => $this->bankName,
                'accountName' => $this->accountName,
                'number' => $this->number,
                'initialBalance' => $this->initialBalance,
                'currentBalance' => $this->currentBalance,
            ]);
        } else {
            Bank::where('id', $this->id)->update([
                'bankName' => $this->bankName,
                'accountName' => $this->accountName,
                'number' => $this->number,
                'initialBalance' => $this->initialBalance,
                'currentBalance' => $this->currentBalance,
            ]);
        }
    }
    public function render()
    {
        $this->safe = \App\Models\Safe::sum('currentBalance');
        $this->bank = \App\Models\Bank::sum('currentBalance');
        $this->banks = Bank::where('bankName', 'LIKE', '%'.$this->bankSearch.'%')->get();
        return view('livewire.safe');
    }
}
