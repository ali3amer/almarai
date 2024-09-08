<?php

namespace App\Livewire;

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
    public int $id = 0;
    public int $debtId = 0;
    public string $clientName = '';
    public string $phone = '';
    public string $search = '';
    public string|null $note = '';
    public $initialBalance = 0;
    public $amount = 0;
    public string $bank = '';
    public string $startingDate = '';
    public Collection $banks;
    public null|int $bank_id = null;
    public Collection $clients;
    public array $currentClient = [];
    public array $debts = [];
    public string $type = 'pay';
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
            'clientName' => 'required|unique:clients,clientName,' . $this->id
        ];
    }

    protected function messages()
    {
        return [
            'clientName.required' => 'الرجاء إدخال إسم العميل',
            'clientName.unique' => 'هذا العميل موجود مسبقاً'
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
                \App\Models\Client::create(['clientName' => $this->clientName, 'phone' => $this->phone, 'initialBalance' => floatval($this->initialBalance), 'startingDate' => $this->startingDate, 'blocked' => $this->blocked, 'cash' => $this->cash]);
                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
            } else {
                $client = \App\Models\Client::find($id);
                $client->clientName = $this->clientName;
                $client->phone = $this->phone;
                $client->note = $this->note;
                $client->initialBalance = floatval($this->initialBalance);
                $client->save();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
            }
            $this->id = 0;
            $this->clientName = '';
            $this->phone = '';
            $this->initialBalance = 0;
            $this->note = '';
            $this->blocked = false;
            $this->cash = false;
        }

    }

    public function changeBlocked($client)
    {
        $this->blocked = !$client['blocked'];
        \App\Models\Client::where('id', $client['id'])->update(['blocked' => $this->blocked]);
        $this->resetData();
        $this->alert('success', "تم تغيير حالة العميل النقدي", ['timerProgressBar' => true]);

    }

    public function changeCash($client)
    {
        $this->cash = !$client['cash'];
        if ($this->cash) {
            \App\Models\Client::where('cash', $this->cash)->update(['cash' => false]);
        }
        \App\Models\Client::where('id', $client['id'])->update(['cash' => $this->cash]);
        $this->resetData();
        $this->alert('success', "تم تغيير العميل النقدي", ['timerProgressBar' => true]);
    }

    public function edit($client)
    {
        $this->id = $client['id'];
        $this->clientName = $client['clientName'];
        $this->phone = $client['phone'];
        $this->initialBalance = $client['initialBalance'];
        $this->blocked = $client['blocked'];
        $this->note = $client['note'];
        $this->cash = $client['cash'];
        $this->startingDate = $client['startingDate'];

    }

    public function deleteMessage($client)
    {
        $this->confirm("  هل توافق على حذف العميل  " . $client['clientName'] . "؟", [
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
        $client = \App\Models\Client::find($data['inputAttributes']['id']);
        $client->delete();
        $this->alert('success', 'تم الحذف بنجاح', ['timerProgressBar' => true]);
    }

    public function showDebts($client)
    {
        $this->currentClient = $client;

        $this->debts = (new \App\Models\Sale)->getMovements($this->currentClient['id'], 'client')->toArray();

        $this->currentBalance = \App\Models\Client::find($this->currentClient['id'])->currentBalance;

    }

    public function saveDebt()
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
                if ($this->type == 'debt') {
                    $note = 'تم إستلاف مبلغ';
                } else {
                    $note = 'تم إستلام مبلغ';
                }
                if (floatval($this->amount) != 0) {
                    $debt = SaleDebt::create([
                        'client_id' => $this->currentClient['id'],
                        'type' => $this->type,
                        'amount' => $this->amount,
                        'discount' => 0,
                        'service' => 0,
                        'payment' => $this->payment,
                        'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                        'bank' => $this->bank,
                        'due_date' => $this->due_date,
                        'note' => $this->note == '' ? $note : $this->note,
                        'user_id' => auth()->id(),
                    ]);
                    $this->chooseDebt($debt->toArray());
                }

                if (floatval($this->discount) != 0) {
                    $debt = SaleDebt::create([
                        'client_id' => $this->currentClient['id'],
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

                if (floatval($this->service) != 0) {
                    $debt = SaleDebt::create([
                        'client_id' => $this->currentClient['id'],
                        'type' => "debt",
                        'amount' => 0,
                        'service' => $this->service,
                        'payment' => 'cash',
                        'bank_id' => null,
                        'bank' => '',
                        'due_date' => $this->due_date,
                        'note' => $this->note == "" ? "تمت إضافة خدمه" : $this->note,
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
                    $debt->due_date = $this->due_date;
                    $debt->user_id = auth()->id();

                    $debt->save();

                $this->alert('success', 'تم تعديل الدفعيه بنجاح', ['timerProgressBar' => true]);

            }
            $this->resetData();

            $this->showReceipt($debt->toArray());

            $this->showDebts($this->currentClient);
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
        $this->amount =  $debt['amount'] ?? ($debt['type'] == "pay" ? $debt['income'] : $debt['expense']);
        $this->payment = $debt['payment'];
        $this->bank = $debt['bank'];
        $this->discount = $debt['futureIncome'] ?? $debt['discount'];
        $this->service = $debt['futureExpense'] ?? $debt['service'];
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

        SaleDebt::where('id', $id)->forceDelete();
        $this->showDebts($this->currentClient);

        $this->alert('success', 'تم حذف الدفعيه بنجاح', ['timerProgressBar' => true]);

    }


    public function resetData($data = null)
    {
        $this->reset('type', 'amount', 'debtId', 'payment', 'bank', 'bank_id', 'due_date', 'blocked', 'cash', 'discount', 'service', 'note', $data);
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
        $this->clients = \App\Models\Client::where('clientName', 'like', '%' . $this->search . '%')->orWhere('phone', 'like', '%' . $this->search . '%')->get();
        return view('livewire.client');
    }
}
