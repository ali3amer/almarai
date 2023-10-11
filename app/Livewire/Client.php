<?php

namespace App\Livewire;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Bank;
use App\Models\ClientDebt;
use App\Models\DebtDetail;
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
    public float $safeBalance = 0;
    public float $bankBalance = 0;
    #[Rule('required|min:2', message: 'قم بإدخال رقم الهاتف')]
    public string $phone = '';
    public string $search = '';
    public string|null $note = '';
    public $initialBalance = 0;
    public $debt_amount = 0;
    public string $bank = '';
    public Collection $banks;
    public null|int $bank_id = 1;
    public Collection $clients;
    public array $currentClient = [];
    public Collection $debts;
    public string $type = 'debt';
    public string $payment = 'cash';
    public string $due_date = '';
    public bool $blocked = false;

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
    }

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Client::create(['clientName' => $this->clientName, 'phone' => $this->phone, 'initialBalance' => floatval($this->initialBalance), 'currentBalance' => floatval($this->initialBalance), 'blocked' => $this->blocked]);
                $this->alert('success', 'تم الحفظ بنجاح', ['timerProgressBar' => true]);
            } else {
                $client = \App\Models\Client::find($id);
                $client->clientName = $this->clientName;
                $client->phone = $this->phone;
                $client->note = $this->note;
                if (\App\Models\Sale::where('client_id', $id)->count() == 0) {
                    $client->initialBalance = floatval($this->initialBalance);
                    $client->currentBalance = floatval($this->initialBalance);

                }
                $client->save();
                $this->alert('success', 'تم التعديل بنجاح', ['timerProgressBar' => true]);
            }
            $this->id = 0;
            $this->clientName = '';
            $this->phone = '';
            $this->initialBalance = 0;
            $this->note = '';
            $this->blocked = false;
        }

    }

    public function changeBlocked($client)
    {
        $this->blocked = !$client['blocked'];
        \App\Models\Client::where('id', $client['id'])->update(['blocked' => $this->blocked]);
        $this->resetData();
    }
    public function edit($client)
    {
        $this->id = $client['id'];
        $this->clientName = $client['clientName'];
        $this->phone = $client['phone'];
        $this->initialBalance = $client['initialBalance'];
        $this->blocked = $client['blocked'];
        $this->note = $client['note'];

    }

    public function deleteMessage($client)
    {
        $this->confirm("  هل توافق على حذف العميل  " . $client['clientName'] .  "؟", [
            'inputAttributes' => ["id"=>$client['id']],
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
        $this->debts = ClientDebt::where('client_id', $client['id'])->get();

    }

    public function saveDebt()
    {
        if ($this->debtId == 0) {
            if ($this->type == 'debt') {
                $note = 'تم شراء بالآجل';
                $debt = $this->debt_amount;
                $paid = 0;
            } else {
                $note = 'تم إستلام مبلغ';
                $paid = $this->debt_amount;
                $debt = 0;
            }
            ClientDebt::create([
                'client_id' => $this->currentClient['id'],
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

            $this->resetData();

            $this->alert('success', 'تم السداد بنجاح', ['timerProgressBar' => true]);

        } else {
            $debt = ClientDebt::where('id', $this->debtId)->first();

            $debt->update([
                'client_id' => $this->currentClient['id'],
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
        $this->debtId = $debt['id'];
        $this->bank_id = $debt['bank_id'];
        $this->type = $debt['type'];
        $this->debt_amount = $debt['type'] == 'debt' ? $debt['debt'] : $debt['paid'];
        $this->payment = $debt['payment'];
        $this->bank = $debt['bank'];
        $this->due_date = $debt['due_date'];
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

        ClientDebt::where('id', $debt['id'])->delete();
        $this->alert('success', 'تم حذف الدفعيه بنجاح', ['timerProgressBar' => true]);

    }


    public function resetData($data = null)
    {
        $this->reset('type', 'debt_amount', 'debtId', 'payment', 'bank', 'due_date', 'blocked', 'note', $data);
    }

    public function render()
    {
        $this->safeBalance = \App\Models\Safe::first()->currentBalance;
        if ($this->bank_id != null) {
            $this->bankBalance = Bank::where('id', $this->bank_id)->first()->currentBalance;
        }
        if ($this->due_date == '') {
            $this->due_date = date('Y-m-d');
        }
        if (!empty($this->currentClient)) {
            $this->debts = ClientDebt::where('client_id', $this->currentClient['id'])->get();
        }
        $this->clients = \App\Models\Client::where('clientName', 'like', '%' . $this->search . '%')->orWhere('phone', 'like', '%' . $this->search . '%')->get();
        return view('livewire.client');
    }
}
