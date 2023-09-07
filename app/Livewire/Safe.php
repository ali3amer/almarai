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
    public int $number = 0;
    public float $firstBalance = 0;
    public float $currentBalance = 0;

    public Collection $banks;
    public string $bankSearch ='';

    public function saveBank() {
        if ($this->id == 0) {
            Bank::create([
                'bankName' => $this->bankName,
                'number' => $this->number,
                'firstBalance' => $this->firstBalance,
                'currentBalance' => $this->currentBalance,
            ]);
        } else {
            Bank::where('id', $this->id)->update([
                'bankName' => $this->bankName,
                'number' => $this->number,
                'firstBalance' => $this->firstBalance,
                'currentBalance' => $this->currentBalance,
            ]);
        }
    }
    public function render()
    {
        $this->banks = Bank::where('bankName', 'LIKE', '%'.$this->bankSearch.'%')->get();
        return view('livewire.safe');
    }
}
