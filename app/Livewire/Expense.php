<?php

namespace App\Livewire;

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
//    #[Rule('required|min:2')]
    public string $expense_date = '';
    public  string $search = '';
    public Collection $expenses;

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Expense::create(['description' => $this->description, 'amount' => $this->amount, 'expense_date' => $this->expense_date]);
            } else {
                $expense = \App\Models\Expense::find($id);
                $expense->description = $this->description;
                $expense->amount = $this->amount;
                $expense->expense_date = $this->expense_date;
                $expense->save();
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
        $this->expense_date = $expense['expense_date'];
    }

    public function delete($id)
    {
        $expense = \App\Models\Expense::find($id);
        $expense->delete();
        $this->id = 0;
        $this->description = '';
        $this->amount = 0;
        $this->expense_date = '';
    }

    public function render()
    {
        if ($this->description == ''){
            $this->expense_date = now('Africa/Khartoum')->format('Y-m-d');
        }
        $this->expenses = \App\Models\Expense::where('description', 'like', '%' . $this->search . '%')->get();

        return view('livewire.expense');
    }
}
