<?php

namespace App\Livewire;
use App\Models\SaleDebt;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Bank;
use App\Models\PurchaseDebt;
use App\Models\DebtDetail;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Supplier extends Component
{
    use LivewireAlert;
    protected $listeners = [
        'delete',
        'deleteDebt'
    ];
    public string $title = 'الموردين';
    public int $id = 0;
    public int $debtId = 0;
    public string $supplierName = '';
    #[Rule('required|min:2', message: 'قم بإدخال رقم الهاتف')]
    public string $phone = '';
    public string $search = '';
    public string|null $note = '';
    public $initialBalance = 0;
    public $debt_amount = 0;
    public string $bank = '';
    public Collection $banks;
    public null|int $bank_id = 1;
    public Collection $suppliers;
    public array $currentSupplier = [];
    public Collection $debts;
    public string $type = 'debt';
    public string $debtType = 'purchases';
    public string $payment = 'cash';
    public string $due_date = '';
    public bool $blocked = false;
    public string $startingDate = '';
    public float $currentBalance = 0;
    public array $currentDebt = [];

    protected function rules()
    {
        return [
            'supplierName' => 'required|unique:suppliers,supplierName,' . $this->id
        ];
    }

    protected function messages()
    {
        return [
            'supplierName.required' => 'الرجاء إدخال إسم المورد',
            'supplierName.unique' => 'هذا المورد موجود مسبقاً'
        ];
    }

    public function mount()
    {
        $this->banks = Bank::all();
    }

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Supplier::create(['supplierName' => $this->supplierName, 'phone' => $this->phone, 'initialBalance' => floatval($this->initialBalance), 'startingDate' => $this->startingDate, 'currentBalance' => floatval($this->initialBalance), 'blocked' => $this->blocked]);
                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
            } else {
                $supplier = \App\Models\Supplier::find($id);
                $supplier->supplierName = $this->supplierName;
                $supplier->phone = $this->phone;
                $supplier->note = $this->note;
                if (\App\Models\Sale::where('supplier_id', $id)->count() == 0) {
                    $supplier->initialBalance = floatval($this->initialBalance);
                }
                $supplier->save();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
            }
            $this->id = 0;
            $this->supplierName = '';
            $this->phone = '';
            $this->initialBalance = 0;
            $this->note = '';
            $this->blocked = false;
        }

    }

    public function changeBlocked($supplier)
    {
        $this->blocked = !$supplier['blocked'];
        \App\Models\Supplier::where('id', $supplier['id'])->update(['blocked' => $this->blocked]);
        $this->resetData();
    }
    public function edit($supplier)
    {
        $this->id = $supplier['id'];
        $this->supplierName = $supplier['supplierName'];
        $this->phone = $supplier['phone'];
        $this->initialBalance = $supplier['initialBalance'];
        $this->blocked = $supplier['blocked'];
        $this->note = $supplier['note'];

    }

    public function deleteMessage($supplier)
    {
        $this->confirm("  هل توافق على حذف المورد  " . $supplier['supplierName'] .  "؟", [
            'inputAttributes' => ["id"=>$supplier['id']],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "delete",
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }

    public function delete($data)
    {
        $supplier = \App\Models\Supplier::find($data['inputAttributes']['id']);
        $supplier->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
    }

    public function showDebts($supplier)
    {
        $this->currentSupplier = $supplier;
        $this->debts = PurchaseDebt::where('supplier_id', $supplier['id'])->get();
        $this->currentBalance = $this->debts->sum('debt') - $this->debts->sum('paid');
    }

    public function saveDebt()
    {
        if ($this->debtId == 0) {
            if ($this->type == 'debt') {
                $note = 'تم إستلاف مبلغ';
                $debt = $this->debt_amount;
                $paid = 0;
            } else {
                $note = 'تم إستلام مبلغ';
                $paid = $this->debt_amount;
                $debt = 0;
            }

            if ($this->debtType == 'purchases') {
                PurchaseDebt::create([
                    'supplier_id' => $this->currentSupplier['id'],
                    'type' => $this->type,
                    'debt' => $debt,
                    'paid' => $paid,
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                    'bank' => $this->bank,
                    'due_date' => $this->due_date,
                    'note' => $this->note == '' ? $note : $this->note,
                    'user_id' => auth()->id(),
                ]);
            } else {
                SaleDebt::create([
                    'supplier_id' => $this->currentSupplier['id'],
                    'type' => $this->type,
                    'debt' => $debt,
                    'paid' => $paid,
                    'payment' => $this->payment,
                    'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                    'bank' => $this->bank,
                    'due_date' => $this->due_date,
                    'note' => $this->note == '' ? $note : $this->note,
                    'user_id' => auth()->id(),
                ]);
            }

            $this->resetData();

            $this->alert('success', 'تم السداد بنجاح', ['timerProgressBar' => true]);

        } else {
            $debt = PurchaseDebt::where('id', $this->debtId)->first();

            $debt->update([
                'supplier_id' => $this->currentSupplier['id'],
                'type' => $this->type,
                'debt' => $this->type == 'debt' ? $this->debt_amount : 0,
                'paid' => $this->type == 'pay' ? $this->debt_amount : 0,
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'bank' => $this->bank,
                'due_date' => $this->due_date,
                'user_id' => auth()->id(),
            ]);

            $this->resetData();
            $this->alert('success', 'تم تعديل الدفعيه بنجاح', ['timerProgressBar' => true]);

        }
    }

    public function chooseDebt($debt)
    {
        $this->currentDebt = $debt;
//        $this->debtId = $debt['id'];
//        $this->bank_id = $debt['bank_id'];
//        $this->type = $debt['type'];
//        $this->debt_amount = $debt['type'] == 'debt' ? $debt['debt'] : $debt['paid'];
//        $this->payment = $debt['payment'];
//        $this->bank = $debt['bank'];
//        $this->due_date = $debt['due_date'];
    }

    public function deleteDebtMessage($debt)
    {
        $this->confirm("  هل توافق على الحذف؟", [
            'inputAttributes' => ["debt"=>$debt],
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'موافق',
            'onConfirmed' => "deleteDebt",
            'showCancelButton' => true,
            'cancelButtonText' => 'إلغاء',
            'confirmButtonColor' => '#dc2626',
            'cancelButtonColor' => '#4b5563'
        ]);
    }
    public function deleteDebt($data)
    {
        $debt = $data['inputAttributes']['debt'];

        PurchaseDebt::where('id', $debt['id'])->delete();
        $this->alert('success', 'تم حذف الدفعيه بنجاح', ['timerProgressBar' => true]);

    }


    public function resetData($data = null)
    {
        $this->reset('type', 'debt_amount', 'debtId', 'payment', 'bank', 'due_date', 'blocked', 'note', $data);
    }

    public function render()
    {
        if ($this->due_date == '') {
            $this->due_date = date('Y-m-d');
        }

        if ($this->startingDate == '') {
            $this->startingDate = date('Y-m-d');
        }

        if (!empty($this->currentSupplier)) {
            if ($this->debtType == 'purchases') {
                $this->debts = PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->get();
            } else {
                $this->debts = SaleDebt::where('supplier_id', $this->currentSupplier['id'])->get();
            }
            $this->currentBalance = $this->debts->sum('debt') - $this->debts->sum('paid');
        }
        $this->suppliers = \App\Models\Supplier::where('supplierName', 'like', '%' . $this->search . '%')->orWhere('phone', 'like', '%' . $this->search . '%')->get();
        return view('livewire.supplier');
    }
}
