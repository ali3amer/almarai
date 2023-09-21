<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\BankDetail;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Expense extends Component
{

    public string $title = 'المصروفات';
    public int $id = 0;
    #[Rule('required|min:2')]
    public string $description = '';
    #[Rule('required|min:2')]
    public float $amount = 0;
    public string $payment = 'cash';
    public string $bank = '';
    public int $bank_id = 1;
//    #[Rule('required|min:2')]
    public string $expense_date = '';
    public  string $search = '';
    public Collection $expenses;
    public Collection $banks;

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Expense::create([
                    'description' => $this->description,
                    'amount' => $this->amount,
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ?$this->bank_id : null,
                    'bank' => $this->bank,
                    'expense_date' => $this->expense_date
                ]);

                if ($this->payment == 'cash') {
                    \App\Models\Safe::first()->decrement('currentBalance', $this->amount);
                } else {
                    Bank::where('id', $this->bank_id)->decrement('currentBalance', $this->amount);
                }

                session()->flash('success', 'تم الحفظ بنجاح');

            } else {
                $expense = \App\Models\Expense::find($id);

                if ($expense['payment'] == 'cash') {
                    \App\Models\Safe::first()->increment('currentBalance', $expense['amount']);
                } else {
                    Bank::where('id', $expense['bank_id'])->decrement('currentBalance', $expense['amount']);
                }

                $expense->description = $this->description;
                $expense->amount = $this->amount;
                $expense->payment = $this->payment;
                $expense->bank_id = $this->payment == 'bank' ? $this->bank_id : null;
                $expense->bank = $this->bank;
                $expense->expense_date = $this->expense_date;
                if ($this->payment == 'cash') {
                    \App\Models\Safe::first()->decrement('currentBalance', $this->amount);
                } else {
                    Bank::where('id', $this->bank_id)->decrement('currentBalance', $this->amount);
                }
                $expense->save();
                session()->flash('success', 'تم التعديل بنجاح');

            }
            $this->id = 0;
            $this->description = '';
            $this->amount = 0;
            $this->expense_date = '';
        }

    }

    public function edit($expense)
    {
        $this->id = $expense['id'];
        $this->description = $expense['description'];
        $this->amount = $expense['amount'];
        $this->payment = $expense['payment'];
        $this->bank_id = $expense['bank_id'];
        $this->bank = $expense['bank'];
        $this->expense_date = $expense['expense_date'];
    }

    public function delete($id)
    {
        $expense = \App\Models\Expense::find($id);
        if ($expense['payment'] == 'cash') {
            \App\Models\Safe::first()->increment('currentBalance', $expense['amount']);
        } else {
            Bank::where('id', $expense['bank_id'])->decrement('currentBalance', $expense['amount']);
        }
        $expense->delete();
        session()->flash('success', 'تم الحذف بنجاح');

        $this->id = 0;
        $this->description = '';
        $this->amount = 0;
        $this->expense_date = '';
    }

    public function render()
    {
        if ($this->description == ''){
            $this->expense_date = now();
        }
        $this->banks = Bank::all();
        $this->expenses = \App\Models\Expense::where('description', 'like', '%' . $this->search . '%')->get();

        return view('livewire.expense');
    }
}
