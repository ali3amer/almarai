<?php

namespace App\Livewire;

use App\Models\DepositDebt;
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
    public string $name = '';
    public string $phone = '';
    public string $search = '';
    public string|null $note = '';
    public $initialSalesBalance = 0;
    public $initialPurchasesBalance = 0;
    public $initialDepositsBalance = 0;
    public $discount = 0;
    public $service = 0;
    public bool $cash = false;
    public $amount = 0;
    public string $bank = '';
    public Collection $banks;
    public null|int $bank_id = null;
    public Collection $suppliers;
    public array $currentSupplier = [];
    public array $debts = [];
    public string $type = 'pay';
    public string $debtType = 'purchases';
    public string $payment = 'cash';
    public string $due_date = '';
    public bool $blocked = false;
    public string $startingDate = '';
    public float $currentBalance = 0;
    public array $currentDebt = [];
    public bool $create = false;
    public bool $read = false;
    public bool $update = false;
    public bool $delete = false;
    public array $currentReceipt = [];

    protected function rules()
    {
        return [
            'name' => 'required|unique:people,name,' . $this->id
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'الرجاء إدخال إسم المورد',
            'name.unique' => 'هذا المورد موجود مسبقاً'
        ];
    }

    public function mount()
    {
        $this->banks = Bank::all();
        if ($this->banks->count() != 0) {
            $this->bank_id = $this->banks->first()->id;
        }
        $user = auth()->user();
        $this->create = $user->hasPermission('suppliers-create');
        $this->read = $user->hasPermission('suppliers-read');
        $this->update = $user->hasPermission('suppliers-update');
        $this->delete = $user->hasPermission('suppliers-delete');

    }

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\People::create(['name' => $this->name, 'phone' => $this->phone, 'initialSalesBalance' => floatval($this->initialSalesBalance), 'initialPurchasesBalance' => floatval($this->initialPurchasesBalance), 'initialDepositsBalance' => floatval($this->initialDepositsBalance), 'startingDate' => $this->startingDate, 'type' => 'supplier', 'blocked' => $this->blocked, 'cash' => $this->cash]);
                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
            } else {
                $supplier = \App\Models\People::find($id);
                $supplier->name = $this->name;
                $supplier->phone = $this->phone;
                $supplier->note = $this->note;
                $supplier->initialPurchasesBalance = floatval($this->initialPurchasesBalance);
                $supplier->initialSalesBalance = floatval($this->initialSalesBalance);
                $supplier->initialDepositsBalance = floatval($this->initialDepositsBalance);
                $supplier->save();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
            }
            $this->resetData();
        }

    }

    public function changeBlocked($supplier)
    {
        $this->blocked = !$supplier['blocked'];
        \App\Models\People::where('id', $supplier['id'])->update(['blocked' => $this->blocked]);
        $this->resetData();
        $this->alert('success', "تم تغيير حالة المورد النقدي", ['timerProgressBar' => true]);

    }

    public function changeCash($supplier)
    {
        $this->cash = !$supplier['cash'];
        if ($this->cash) {
            \App\Models\People::where('cash', $this->cash)->where("type", "supplier")->update(['cash' => false]);
        }
        \App\Models\People::where('id', $supplier['id'])->update(['cash' => $this->cash]);
        $this->resetData();
        $this->alert('success', "تم تغيير المورد النقدي", ['timerProgressBar' => true]);

    }

    public function edit($supplier)
    {
        $this->id = $supplier['id'];
        $this->name = $supplier['name'];
        $this->phone = $supplier['phone'];
        $this->initialSalesBalance = $supplier['initialSalesBalance'];
        $this->initialPurchasesBalance = $supplier['initialPurchasesBalance'];
        $this->initialDepositsBalance = $supplier['initialDepositsBalance'];
        $this->blocked = $supplier['blocked'];
        $this->note = $supplier['note'];
        $this->cash = $supplier['cash'];
        $this->startingDate = $supplier['startingDate'];

    }

    public function deleteMessage($supplier)
    {
        $this->confirm("  هل توافق على حذف المورد  " . $supplier['name'] . "؟", [
            'inputAttributes' => ["id" => $supplier['id']],
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
        $supplier = \App\Models\People::find($data['inputAttributes']['id']);
        $supplier->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
    }

    public function showDebts($supplier = null)
    {
        if ($supplier == null) {
            $supplier = $this->currentSupplier;
        }
        $this->currentSupplier = $supplier;
        if ($this->debtType == 'purchases') {
            $this->debts = (new \App\Models\Purchase)->getMovements($this->currentSupplier['id'])->toArray();
            $this->currentBalance = \App\Models\People::find($this->currentSupplier['id'])->currentPurchasesBalance;
        } elseif ($this->debtType == 'sales') {
            $this->debts = (new \App\Models\Sale)->getMovements($this->currentSupplier['id'], 'supplier')->toArray();
            $this->currentBalance = \App\Models\People::find($this->currentSupplier['id'])->currentSalesBalance;
        } elseif ($this->debtType == 'deposits') {
            $this->debts = (new \App\Models\Deposit)->getMovements($this->supplier['id'], 'currentSupplier')->toArray();
            $this->currentBalance = \App\Models\People::find($this->currentSupplier['id'])->currentDepositsBalance;
        }
    }

    public function saveSaleDebt()
    {

        if ($this->type == 'debt') {
            $note = 'تم إستلاف مبلغ';
        } elseif ($this->type == "discount") {
            $note = 'تم خصم مبلغ';
        } else {
            $note = 'تم إستلام مبلغ';
        }

        if ($this->type == "debt" && floatval($this->amount) > floatval(session($this->payment == "cash" ? "safeBalance" : "bankBalance"))) {
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
            if ($this->debtId == 0) {

                if (floatval($this->amount) != 0) {
                    $debt = SaleDebt::create([
                        'people_id' => $this->currentSupplier['id'],
                        'type' => $this->type,
                        'amount' => $this->amount,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'bank' => $this->bank,
                        'due_date' => $this->due_date,
                        'note' => $this->note == '' ? $note : $this->note,
                        'user_id' => auth()->id(),
                    ]);
                }

                $this->alert('success', 'تم السداد بنجاح', ['timerProgressBar' => true]);
            } else {
                $debt = SaleDebt::where('id', $this->debtId)->first();

                $debt->type = $this->type;
                $debt->amount = $this->amount;
                $debt->payment = $this->payment;
                $debt->bank_id = $this->payment == 'bank' ? $this->bank_id : null;
                $debt->bank = $this->bank;
                $debt->due_date = $this->due_date;
                $debt->user_id = auth()->id();
                $debt->save();
                $this->alert('success', 'تم تعديل الدفعيه بنجاح', ['timerProgressBar' => true]);

            }
            $this->showReceipt($debt->toArray());

        }
        $this->resetData();
        $this->showDebts($this->currentSupplier);

    }

    public function savePurchaseDebt()
    {
        if ($this->type == 'debt') {
            $note = 'تم إستلاف مبلغ';
        } elseif ($this->type == "discount") {
            $note = 'تم خصم مبلغ';
        } else {
            $note = 'تم دفع مبلغ';
        }

        if ($this->type == "pay" && floatval($this->amount) > floatval(session($this->payment == "cash" ? "safeBalance" : "bankBalance"))) {
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
            if ($this->debtId == 0) {

                if (floatval($this->amount) != 0) {
                    $debt = PurchaseDebt::create([
                        'people_id' => $this->currentSupplier['id'],
                        'type' => $this->type,
                        'amount' => $this->amount,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'bank' => $this->bank,
                        'due_date' => $this->due_date,
                        'note' => $this->note == '' ? $note : $this->note,
                        'user_id' => auth()->id(),
                    ]);
                }

                $this->alert('success', 'تم السداد بنجاح', ['timerProgressBar' => true]);

            } else {

                $debt = PurchaseDebt::where('id', $this->debtId)->first();

                $debt->type = $this->type;
                $debt->amount = $this->amount;
                $debt->payment = $this->payment;
                $debt->bank_id = $this->payment == 'bank' ? $this->bank_id : null;
                $debt->bank = $this->bank;
                $debt->due_date = $this->due_date;
                $debt->user_id = auth()->id();
                $debt->save();

                $this->alert('success', 'تم تعديل الدفعيه بنجاح', ['timerProgressBar' => true]);

            }
            $this->showReceipt($debt->toArray());
        }

        $this->resetData();
        $this->showDebts($this->currentSupplier);

    }

    public function saveDepositDebt()
    {
        if ($this->type == "debt" && floatval($this->amount) > floatval(session($this->payment == "cash" ? "safeBalance" : "bankBalance"))) {
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
            if ($this->debtId == 0) {
                if ($this->type == 'pay') {
                    $note = 'تم إيداع مبلغ';
                } else {
                    $note = 'تم سحب مبلغ';
                }
                if (floatval($this->amount) != 0) {
                    $debt = DepositDebt::create([
                        'people_id' => $this->currentSupplier['id'],
                        'type' => $this->type,
                        'amount' => $this->amount,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'bank' => $this->bank,
                        'due_date' => $this->due_date,
                        'note' => $this->note == '' ? $note : $this->note,
                        'user_id' => auth()->id(),
                    ]);
                }

                $this->alert('success', $note, ['timerProgressBar' => true]);

            } else {
                $debt = DepositDebt::where('id', $this->debtId)->first();

                $debt->type = $this->type;
                $debt->amount = $this->amount;
                $debt->payment = $this->payment;
                $debt->bank_id = $this->payment == 'bank' ? $this->bank_id : null;
                $debt->bank = $this->bank;
                $debt->due_date = $this->due_date;
                $debt->user_id = auth()->id();

                $debt->save();

                $this->alert('success', 'تم تعديل الدفعيه بنجاح', ['timerProgressBar' => true]);

            }
            $this->resetData();

            $this->showDebts($this->currentSupplier);
            $this->showReceipt($debt->toArray());

        }

    }


    public function showReceipt($debt)
    {
        $this->currentReceipt = (array)$debt;
    }

    public function chooseDebt($debt)
    {
        $this->currentDebt = $debt;
        $this->debtId = $debt['id'];
        $this->bank_id = $debt['bank_id'];
        $this->type = $debt['type'];
        $this->amount = $debt['amount'] ?? ($debt['type'] == "pay" ? $debt['income'] : $debt['expense']);
        $this->payment = $debt['payment'];
        $this->bank = $debt['bank'];
        $this->due_date = $debt['due_date'];
    }

    public function deleteDebtMessage($debt)
    {
        $this->confirm("  هل توافق على الحذف؟", [
            'inputAttributes' => ["id" => $debt],
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
        $id = $data['inputAttributes']['id'];

        if ($this->debtType == 'purchases') {
            PurchaseDebt::where('id', $id)->forceDelete();
        } elseif ($this->debtType == 'sales') {
            SaleDebt::where('id', $id)->forceDelete();
        } elseif ($this->debtType == 'deposits') {
            DepositDebt::where('id', $id)->forceDelete();
        }
        $this->showDebts($this->currentSupplier);
        $this->alert('success', 'تم حذف الدفعيه بنجاح', ['timerProgressBar' => true]);
    }

    public function resetData($data = null)
    {
        $this->reset('id', 'name', 'phone', 'type', 'amount', 'debtId', 'payment', 'bank', 'bank_id', 'cash', 'due_date', 'blocked', 'initialSalesBalance', 'initialPurchasesBalance', 'initialDepositsBalance', 'discount', 'service', 'note', $data);
    }

    public function render()
    {
        if ($this->payment == "bank" && $this->bank_id == null) {
            if ($this->banks->count() != 0) {
                $this->bank_id = $this->banks->first()->id;
            }
        }
        if ($this->due_date == '') {
            $this->due_date = session("date");
        }

        if ($this->startingDate == '') {
            $this->startingDate = session("date");
        }

        $this->suppliers = \App\Models\People::where("type", "supplier")->where('name', 'like', '%' . $this->search . '%')->get();
        return view('livewire.supplier');
    }
}
