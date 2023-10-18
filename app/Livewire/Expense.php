<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\BankDetail;
use Illuminate\Database\Eloquent\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Expense extends Component
{
    use LivewireAlert;
    protected $listeners = [
        'delete'
    ];
    public string $title = 'المصروفات';
    public int $id = 0;
    #[Rule('required', message: 'هذا الحقل مطلوب')]
    public string $description = '';
    #[Rule('required', message: 'هذا الحقل مطلوب')]
    public $amount = 0;
    public string $payment = 'cash';
    public string $bank = '';
    public int|null $bank_id = 1;
    public string $expense_date = '';
    public  string $search = '';
    public Collection $expenses;
    public Collection $banks;

    public function mount()
    {
        $this->banks = Bank::all();
    }
    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Expense::create([
                    'description' => $this->description,
                    'amount' => floatval($this->amount),
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ?$this->bank_id : null,
                    'bank' => $this->bank,
                    'expense_date' => $this->expense_date
                ]);


                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

            } else {
                $expense = \App\Models\Expense::find($id);

                $expense->description = $this->description;
                $expense->amount = floatval($this->amount);
                $expense->payment = $this->payment;
                $expense->bank_id = $this->payment == 'bank' ? $this->bank_id : null;
                $expense->bank = $this->bank;
                $expense->expense_date = $this->expense_date;

                $expense->save();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);

            }

            $this->resetData();
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

    public function deleteMessage($expense)
    {
        $this->confirm("  هل توافق على حذف   " . $expense['description'] . "؟", [
            'inputAttributes' => ["id" => $expense['id']],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "delete",
            "value" => $expense['id'],
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }

    public function delete($data)
    {
        $expense = \App\Models\Expense::find($data['inputAttributes']['id']);

        $expense->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);

    }

    public function resetData() {
        $this->reset('id', 'description', 'amount', 'expense_date');
    }

    public function render()
    {
        if ($this->description == ''){
            $this->expense_date = date('Y-m-d');
        }
        $this->expenses = \App\Models\Expense::where('description', 'like', '%' . $this->search . '%')->get();

        return view('livewire.expense');
    }
}
