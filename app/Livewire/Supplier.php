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
    public string $phone = '';
    public string $search = '';
    public string|null $note = '';
    public $initialBalance = 0;
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
    public $initialSalesBalance = 0;
    public bool $create = false;
    public bool $read = false;
    public bool $update = false;
    public bool $delete = false;
    public array $currentReceipt = [];

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
                \App\Models\Supplier::create(['supplierName' => $this->supplierName, 'phone' => $this->phone, 'initialBalance' => floatval($this->initialBalance), 'startingDate' => $this->startingDate, 'initialSalesBalance' => floatval($this->initialSalesBalance), 'blocked' => $this->blocked, 'cash' => $this->cash]);
                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
            } else {
                $supplier = \App\Models\Supplier::find($id);
                $supplier->supplierName = $this->supplierName;
                $supplier->phone = $this->phone;
                $supplier->note = $this->note;
                $supplier->initialBalance = floatval($this->initialBalance);
                $supplier->initialSalesBalance = floatval($this->initialSalesBalance);
                $supplier->save();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
            }
            $this->id = 0;
            $this->supplierName = '';
            $this->phone = '';
            $this->initialBalance = 0;
            $this->initialSalesBalance = 0;
            $this->note = '';
            $this->blocked = false;
            $this->cash = false;
        }

    }

    public function changeBlocked($supplier)
    {
        $this->blocked = !$supplier['blocked'];
        \App\Models\Supplier::where('id', $supplier['id'])->update(['blocked' => $this->blocked]);
        $this->resetData();
        $this->alert('success', "تم تغيير حالة المورد النقدي", ['timerProgressBar' => true]);

    }

    public function changeCash($supplier)
    {
        $this->cash = !$supplier['cash'];
        if ($this->cash) {
            \App\Models\Supplier::where('cash', $this->cash)->update(['cash' => false]);
        }
        \App\Models\Supplier::where('id', $supplier['id'])->update(['cash' => $this->cash]);
        $this->resetData();
        $this->alert('success', "تم تغيير المورد النقدي", ['timerProgressBar' => true]);

    }

    public function edit($supplier)
    {
        $this->id = $supplier['id'];
        $this->supplierName = $supplier['supplierName'];
        $this->phone = $supplier['phone'];
        $this->initialBalance = $supplier['initialBalance'];
        $this->initialSalesBalance = $supplier['initialSalesBalance'];
        $this->blocked = $supplier['blocked'];
        $this->note = $supplier['note'];
        $this->cash = $supplier['cash'];
        $this->startingDate = $supplier['startingDate'];

    }

    public function deleteMessage($supplier)
    {
        $this->confirm("  هل توافق على حذف المورد  " . $supplier['supplierName'] . "؟", [
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
        $supplier = \App\Models\Supplier::find($data['inputAttributes']['id']);
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
            $this->currentBalance = \App\Models\Supplier::find($this->currentSupplier['id'])->currentBalance;
        } else {
            $this->debts = (new \App\Models\Sale)->getMovements($this->currentSupplier['id'], 'supplier')->toArray();
            $this->currentBalance = \App\Models\Supplier::find($this->currentSupplier['id'])->currentSalesBalance;
        }
    }

    public function saveSaleDebt()
    {

        if ($this->type == 'debt') {
            $note = 'تم إستلاف مبلغ';
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
                        'supplier_id' => $this->currentSupplier['id'],
                        'type' => $this->type,
                        'amount' => $this->amount,
                        'payment' => $this->payment,
                        'discount' => 0,
                        'service' => 0,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'bank' => $this->bank,
                        'due_date' => $this->due_date,
                        'note' => $this->note == '' ? $note : $this->note,
                        'user_id' => auth()->id(),
                    ]);
                }

                if (floatval($this->discount) != 0) {
                    $debt = SaleDebt::create([
                        'supplier_id' => $this->currentSupplier['id'],
                        'type' => "pay",
                        'amount' => 0,
                        'discount' => $this->discount,
                        'service' => 0,
                        'payment' => 'cash',
                        'bank_id' => null,
                        'bank' => '',
                        'due_date' => $this->due_date,
                        'note' => "تم تخفيض مبلغ ",
                        'user_id' => auth()->id(),
                    ]);
                }

                if (floatval($this->service) != 0) {
                    $debt = SaleDebt::create([
                        'supplier_id' => $this->currentSupplier['id'],
                        'type' => "debt",
                        'amount' => 0,
                        'service' => $this->service,
                        'payment' => 'cash',
                        'bank_id' => null,
                        'bank' => '',
                        'due_date' => $this->due_date,
                        'note' => $this->note == "" ? "تم إضافة خدمة" : $note,
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
                $debt->discount = $this->discount;
                $debt->service = $this->service;
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
                        'supplier_id' => $this->currentSupplier['id'],
                        'type' => $this->type,
                        'amount' => $this->amount,
                        'discount' => 0,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'bank' => $this->bank,
                        'due_date' => $this->due_date,
                        'note' => $this->note == '' ? $note : $this->note,
                        'user_id' => auth()->id(),
                    ]);
                }

                if (floatval($this->discount) != 0) {
                    $debt = PurchaseDebt::create([
                        'supplier_id' => $this->currentSupplier['id'],
                        'type' => "pay",
                        'amount' => 0,
                        'discount' => $this->discount,
                        'payment' => 'cash',
                        'bank_id' => null,
                        'bank' => '',
                        'due_date' => $this->due_date,
                        'note' => "تم تخفيض مبلغ ",
                        'user_id' => auth()->id(),
                    ]);
                }

                $this->resetData();
                $this->alert('success', 'تم السداد بنجاح', ['timerProgressBar' => true]);

            } else {

                $debt = PurchaseDebt::where('id', $this->debtId)->first();

                    $debt->type = $this->type;
                    $debt->amount = $this->amount;
                    $debt->payment = $this->payment;
                    $debt->bank_id = $this->payment == 'bank' ? $this->bank_id : null;
                    $debt->bank = $this->bank;
                    $debt->discount = $this->discount;
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
        $this->discount = $debt['futureIncome'] ?? $debt['discount'];
        $this->service = $debt['futureExpense'] ?? $debt['service'];
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
        } else {
            SaleDebt::where('id', $id)->forceDelete();
        }
        $this->showDebts($this->currentSupplier);
        $this->alert('success', 'تم حذف الدفعيه بنجاح', ['timerProgressBar' => true]);
    }

    public function resetData($data = null)
    {
        $this->reset('type', 'amount', 'debtId', 'payment', 'bank', 'bank_id', 'cash', 'due_date', 'blocked', 'discount', 'service', 'note', $data);
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

        $this->suppliers = \App\Models\Supplier::where('supplierName', 'like', '%' . $this->search . '%')->orWhere('phone', 'like', '%' . $this->search . '%')->get();
        return view('livewire.supplier');
    }
}
