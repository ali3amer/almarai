<?php

namespace App\Livewire;

use App\Models\DepositDebt;
use App\Models\PurchaseDebt;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Bank;
use App\Models\SaleDebt;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Client extends Component
{
    use LivewireAlert;

    protected $listeners = [
        'delete',
        'deleteDebt'
    ];
    public string $title = 'العملاء';
    public bool $show = false;
    public int $id = 0;
    public int $debtId = 0;
    public string $name = '';
    public string $phone = '';
    public string $search = '';
    public string|null $note = '';
    public $initialSalesBalance = 0;
    public $initialPurchasesBalance = 0;
    public $initialDepositsBalance = 0;
    public $amount = 0;
    public string $bank = '';
    public string $startingDate = '';
    public Collection $banks;
    public null|int $bank_id = null;
    public Collection $clients;
    public array $currentClient = [];
    public array $debts = [];
    public string $type = 'pay';
    public string $debtType = 'sales';
    public string $payment = 'cash';
    public string $due_date = '';
    public bool $blocked = false;
    public float $currentBalance = 0;
    public array $currentDebt = [];
    public $discount = 0;
    public $service = 0;
    public bool $cash = false;
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
            'name.required' => 'الرجاء إدخال إسم العميل',
            'name.unique' => 'هذا العميل موجود مسبقاً'
        ];
    }

    public function mount()
    {
        $this->banks = Bank::all();
        if ($this->banks->count() != 0) {
            $this->bank_id = $this->banks->first()->id;
        }
        $user = auth()->user();
        $this->create = $user->hasPermission('clients-create');
        $this->read = $user->hasPermission('clients-read');
        $this->update = $user->hasPermission('clients-update');
        $this->delete = $user->hasPermission('clients-delete');
    }

    public function save($id)
    {
        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\People::create(['name' => $this->name, 'phone' => $this->phone, 'initialSalesBalance' => floatval($this->initialSalesBalance), 'initialPurchasesBalance' => floatval($this->initialPurchasesBalance), 'initialDepositsBalance' => floatval($this->initialDepositsBalance), 'type' => 'client', 'startingDate' => $this->startingDate, 'blocked' => $this->blocked, 'cash' => $this->cash]);
                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
            } else {
                $client = \App\Models\People::find($id);
                $client->name = $this->name;
                $client->phone = $this->phone;
                $client->note = $this->note;
                $client->initialSalesBalance = floatval($this->initialSalesBalance);
                $client->initialPurchasesBalance = floatval($this->initialPurchasesBalance);
                $client->initialDepositsBalance = floatval($this->initialDepositsBalance);
                $client->save();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
            }
            $this->resetData();

        }

    }

    public function changeBlocked($client)
    {
        $this->blocked = !$client['blocked'];
        \App\Models\People::where('id', $client['id'])->update(['blocked' => $this->blocked]);
        $this->resetData();
        $this->alert('success', "تم تغيير حالة العميل النقدي", ['timerProgressBar' => true]);

    }

    public function changeCash($client)
    {
        $this->cash = !$client['cash'];
        if ($this->cash) {
            \App\Models\People::where('cash', $this->cash)->where("type", "client")->update(['cash' => false]);
        }
        \App\Models\People::where('id', $client['id'])->update(['cash' => $this->cash]);
        $this->resetData();
        $this->alert('success', "تم تغيير العميل النقدي", ['timerProgressBar' => true]);
    }

    public function edit($client)
    {
        $this->id = $client['id'];
        $this->name = $client['name'];
        $this->phone = $client['phone'];
        $this->initialSalesBalance = $client['initialSalesBalance'];
        $this->initialPurchasesBalance = $client['initialPurchasesBalance'];
        $this->initialDepositsBalance = $client['initialDepositsBalance'];
        $this->blocked = $client['blocked'];
        $this->note = $client['note'];
        $this->cash = $client['cash'];
        $this->startingDate = $client['startingDate'];

    }

    public function deleteMessage($client)
    {
        $this->confirm("  هل توافق على حذف العميل  " . $client['name'] . "؟", [
            'inputAttributes' => ["id" => $client['id']],
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
        $client = \App\Models\People::find($data['inputAttributes']['id']);
        $client->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
    }

    public function showDebts($client = null)
    {
        if ($client == null) {
            $client = $this->currentClient;
        }
        $this->currentClient = $client;

        if ($this->debtType == "sales") {
            $this->currentBalance = \App\Models\People::find($this->currentClient['id'])->currentSalesBalance;
            $this->debts = (new \App\Models\Sale)->getMovements($this->currentClient['id'], 'client')->toArray();
        } elseif ($this->debtType == "purchases") {
            $this->currentBalance = \App\Models\People::find($this->currentClient['id'])->currentPurchasesBalance;
            $this->debts = (new \App\Models\Purchase)->getMovements($this->currentClient['id'], 'client')->toArray();
        } elseif ($this->debtType == 'deposits') {
            $this->debts = (new \App\Models\Deposit)->getMovements($this->currentClient['id'], 'client')->where("due_date", session("date"))->toArray();
            $this->currentBalance = \App\Models\People::find($this->currentClient['id'])->currentDepositsBalance;
        }
    }

    public function saveDebt()
    {
        if ($this->debtType == "deposits") {
            $this->saveDepositDebt();
        } elseif ($this->debtType == "sales") {
            $this->saveSaleDebt();
        } elseif ($this->debtType == "purchases") {
            $this->savePurchaseDebt();
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
                        'people_id' => $this->currentClient['id'],
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
        $this->showDebts($this->currentClient);

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
                        'people_id' => $this->currentClient['id'],
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
        $this->showDebts($this->currentClient);

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
                        'people_id' => $this->currentClient['id'],
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

            $this->showDebts($this->currentClient);
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
        $this->debtId = $debt['invoice_id'] ?? $debt['id'];
        $this->bank_id = $debt['bank_id'];
        $this->type = $debt['type'];
        $this->amount = $debt['amount'] ?? ($debt['type'] == "pay" ? $debt['income'] : $debt['expense']);
        $this->payment = $debt['payment'];
        $this->bank = $debt['bank'];
        $this->due_date = $debt['due_date'];
    }

    public function deleteDebtMessage($id)
    {
        $this->confirm("  هل توافق على الحذف؟", [
            'inputAttributes' => ["id" => $id],
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
        $this->showDebts($this->currentClient);

        $this->alert('success', 'تم حذف الدفعيه بنجاح', ['timerProgressBar' => true]);

    }


    public function resetData($data = null)
    {
        $this->reset('type', 'amount', 'debtId', 'payment', 'bank', 'bank_id', 'due_date', 'blocked', 'cash', 'initialSalesBalance', 'initialPurchasesBalance', 'initialDepositBalance', 'discount', 'service', 'note', $data);
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
        $this->clients = \App\Models\People::where("type", "client")->where('name', 'like', '%' . $this->search . '%')->get();
        return view('livewire.client');
    }
}
