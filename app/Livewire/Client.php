<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\ClientDebt;
use App\Models\DebtDetail;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Client extends Component
{
    public string $title = 'العملاء';
    public int $id = 0;
    public int $debtId = 0;
    public string $clientName = '';
    public float $safeBalance = 0;
    public float $bankBalance = 0;
    #[Rule('required|min:2', message: 'قم بإدخال رقم الهاتف')]
    public string $phone = '';
    public string $search = '';
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
                \App\Models\Client::create(['clientName' => $this->clientName, 'phone' => $this->phone, 'initialBalance' => floatval($this->initialBalance), 'currentBalance' => floatval($this->initialBalance)]);
                session()->flash('success', 'تم الحفظ بنجاح');
            } else {
                $client = \App\Models\Client::find($id);
                $client->clientName = $this->clientName;
                $client->phone = $this->phone;
                if (\App\Models\Sale::where('client_id', $id)->count() == 0) {
                    $client->initialBalance = floatval($this->initialBalance);
                    $client->currentBalance = floatval($this->initialBalance);

                }
                $client->save();
                session()->flash('success', 'تم التعديل بنجاح');
            }
            $this->id = 0;
            $this->clientName = '';
            $this->phone = '';
            $this->initialBalance = 0;
        }

    }

    public function edit($client)
    {
        $this->id = $client['id'];
        $this->clientName = $client['clientName'];
        $this->phone = $client['phone'];
        $this->initialBalance = $client['initialBalance'];
    }

    public function delete($id)
    {
        $client = \App\Models\Client::find($id);
        $client->delete();
        session()->flash('success', 'تم الحذف بنجاح');

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
                $this->currentClient['currentBalance'] += $this->debt_amount;
                \App\Models\Client::where('id', $this->currentClient['id'])->increment('currentBalance', $this->debt_amount);

                if ($this->payment == 'cash') {
                    \App\Models\Safe::first()->decrement('currentBalance', $this->debt_amount);
                } else {
                    \App\Models\Bank::where('id', $this->bank_id)->decrement('currentBalance', $this->debt_amount);
                }
            } else {
                $this->currentClient['currentBalance'] -= $this->debt_amount;
                \App\Models\Client::where('id', $this->currentClient['id'])->decrement('currentBalance', $this->debt_amount);

                if ($this->payment == 'cash') {
                    \App\Models\Safe::first()->increment('currentBalance', $this->debt_amount);
                } else {
                    \App\Models\Bank::where('id', $this->bank_id)->increment('currentBalance', $this->debt_amount);
                }
            }
            ClientDebt::create([
                'client_id' => $this->currentClient['id'],
                'type' => $this->type,
                'debt_amount' => $this->debt_amount,
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'bank' => $this->bank,
                'client_balance' => $this->currentClient['currentBalance'],
                'due_date' => $this->due_date,
                'user_id' => auth()->id(),
            ]);

            $this->resetData();

            session()->flash('success', 'تم الحفظ بنجاح');
        } else {
            $debt = ClientDebt::where('id', $this->debtId)->first();
            if ($debt['type'] == 'debt') {
                $this->currentClient['currentBalance'] -= $debt['debt_amount'];
                \App\Models\Client::where('id', $this->currentClient['id'])->decrement('currentBalance', $debt['debt_amount']);


                if ($debt['payment'] == 'cash') {
                    \App\Models\Safe::first()->increment('currentBalance', $debt['debt_amount']);

                } else {
                    \App\Models\Bank::where('id', $this->bank_id)->increment('currentBalance', $debt['debt_amount']);


                }
            } else {
                $this->currentClient['currentBalance'] += $debt['debt_amount'];
                \App\Models\Client::where('id', $this->currentClient['id'])->increment('currentBalance', $debt['debt_amount']);

                if ($debt['payment'] == 'cash') {
                    \App\Models\Safe::first()->decrement('currentBalance', $debt['debt_amount']);

                } else {
                    \App\Models\Bank::where('id', $this->bank_id)->decrement('currentBalance', $debt['debt_amount']);

                }
            }
            $debt->update([
                'type' => $this->type,
                'debt_amount' => $this->debt_amount,
                'payment' => $this->payment,
                'bank_id' => $this->payment == 'bank' ? $this->bank_id : null,
                'bank' => $this->bank,
                'client_balance' => $this->currentClient['currentBalance'],
                'due_date' => $this->due_date,
                'user_id' => auth()->id(),
            ]);

            if ($this->type == 'debt') {

                $this->currentClient['currentBalance'] += $this->debt_amount;
                \App\Models\Client::where('id', $this->currentClient['id'])->increment('currentBalance', $this->debt_amount);

                if ($this->payment == 'cash') {

                    \App\Models\Safe::first()->decrement('currentBalance', $this->debt_amount);
                } else {

                    \App\Models\Bank::where('id', $this->bank_id)->decrement('currentBalance', $this->debt_amount);

                }
            } else {

                $this->currentClient['currentBalance'] -= $this->debt_amount;
                \App\Models\Client::where('id', $this->currentClient['id'])->decrement('currentBalance', $this->debt_amount);

                if ($debt['payment'] == 'cash') {

                    \App\Models\Safe::first()->increment('currentBalance', $this->debt_amount);

                } else {

                    \App\Models\Bank::where('id', $this->bank_id)->increment('currentBalance', $this->debt_amount);

                }
            }

            $this->resetData();

            session()->flash('success', 'تم التعديل بنجاح');
        }
    }

    public function chooseDebt($debt)
    {
        $this->debtId = $debt['id'];
        $this->bank_id = $debt['bank_id'];
        $this->type = $debt['type'];
        $this->debt_amount = $debt['debt_amount'];
        $this->payment = $debt['payment'];
        $this->bank = $debt['bank'];
        $this->due_date = $debt['due_date'];
    }

    public function deleteDebt($debt)
    {
        if ($debt['type'] == 'debt') {
            $this->currentClient['currentBalance'] -= $debt['debt_amount'];
            \App\Models\Client::where('id', $this->currentClient['id'])->decrement('currentBalance', $debt['debt_amount']);

            if ($debt['payment'] == 'cash') {
                \App\Models\Safe::first()->increment('currentBalance', $debt['debt_amount']);

            } else {
                \App\Models\Bank::where('id', $this->bank_id)->increment('currentBalance', $debt['debt_amount']);

            }
        } else {
            $this->currentClient['currentBalance'] += $debt['debt_amount'];
            \App\Models\Client::where('id', $this->currentClient['id'])->increment('currentBalance', $debt['debt_amount']);

            if ($debt['payment'] == 'cash') {
                \App\Models\Safe::first()->decrement('currentBalance', $debt['debt_amount']);

            } else {
                \App\Models\Bank::where('id', $this->bank_id)->decrement('currentBalance', $debt['debt_amount']);
            }
        }
        ClientDebt::where('id', $debt['id'])->delete();
    }


    public function resetData($data = null)
    {
        $this->reset('type', 'debt_amount', 'debtId', 'payment', 'bank', 'due_date', $data);
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
