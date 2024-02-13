<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\BankDetail;
use App\Models\ExpenseOption;
use Illuminate\Database\Eloquent\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Expense extends Component
{
    use LivewireAlert;

    protected $listeners = [
        'delete',
        'deleteOption'
    ];
    public string $title = 'المصروفات';
    public int $id = 0;
    #[Rule('required', message: 'هذا الحقل مطلوب')]
    public string $description = '';
    #[Rule('required', message: 'هذا الحقل مطلوب')]
    public $amount = 0;
    public string $payment = 'cash';
    public string $bank = '';
    public $bank_id = null;
    public $option_id = null;
    public string $expense_date = '';
    public string $search = '';
    public Collection $expenses;
    public Collection $options;
    public Collection $banks;
    public bool $create = false;
    public bool $read = false;
    public bool $update = false;
    public bool $delete = false;
    public bool $optionsMode = false;
    public int $optionId = 0;
    public $optionName = '';
    public $optionSearch = "";


    public function mount()
    {

        $user = auth()->user();
        $this->create = $user->hasPermission('expenses-create');
        $this->read = $user->hasPermission('expenses-read');
        $this->update = $user->hasPermission('expenses-update');
        $this->delete = $user->hasPermission('expenses-delete');
        $this->banks = Bank::all();
        $this->options = ExpenseOption::all();
        if ($this->banks->count() != 0) {
            $this->bank_id = $this->banks->first()->id;
        }
    }

    public function save($id)
    {
        if (floatval($this->amount) > floatval(session($this->payment == "cash" ? "safeBalance" : "bankBalance"))) {
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
            if ($this->validate()) {
                if ($this->id == 0) {
                    \App\Models\Expense::create([
                        'description' => $this->description,
                        'amount' => floatval($this->amount),
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'option_id' => $this->option_id == 0 ? null : $this->option_id,
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
                    $expense->option_id = $this->option_id == 0 ? null : $this->option_id;
                    $expense->bank = $this->bank;
                    $expense->expense_date = $this->expense_date;

                    $expense->save();
                    $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);

                }

                $this->resetData();
            }
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
        $this->option_id = $expense['option_id'] != null ? $expense['option_id'] : 0;
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

    public function resetData()
    {
        $this->reset('id', 'description', 'amount', 'expense_date', 'option_id');
    }

    public function changeMode()
    {
        $this->optionsMode = !$this->optionsMode;
        $this->search = "";
    }

    public function saveOption()
    {
        if ($this->optionId == 0) {
            \App\Models\ExpenseOption::create([
                'optionName' => $this->optionName,
            ]);

            $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);

        } else {
            $option = \App\Models\ExpenseOption::find($this->optionId);

            $option->optionName = $this->optionName;

            $option->save();

            $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);

        }

        $this->optionId = 0;
        $this->optionName = "";

        $this->resetData();


    }

    public function editOption($option)
    {
        $this->optionId = $option['id'];
        $this->optionName = $option['optionName'];
    }

    public function deleteOptionMessage($option)
    {
        $this->confirm("  هل توافق على حذف   " . $option['optionName'] . "؟", [
            'inputAttributes' => ["id" => $option['id']],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "deleteOption",
            "value" => $option['id'],
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }

    public function deleteOption($data)
    {
        $option = \App\Models\ExpenseOption::find($data['inputAttributes']['id']);

        $option->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);

    }

    public function render()
    {
        if ($this->description == '') {
            $this->expense_date = session("date");
        }

        if ($this->optionsMode) {
            $this->options = ExpenseOption::where("optionName", "LIKE", "%" . $this->search . "%")->get();
        } else {
            $this->expenses = \App\Models\Expense::where('description', 'like', '%' . $this->search . '%')->get();
        }
        return view('livewire.expense');
    }
}
