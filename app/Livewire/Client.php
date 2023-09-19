<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Client extends Component
{
    public string $title = 'العملاء';
    public int $id = 0;
    public string $clientName = '';
    #[Rule('required|min:2', message: 'قم بإدخال رقم الهاتف')]
    public string $phone = '';
    public string $search = '';
    public $initialBalance = 0;
    public Collection $clients;

    protected function rules() {
        return [
            'clientName' => 'required|unique:clients,clientName,'.$this->id
        ];
    }

    protected function messages() {
        return [
            'clientName.required' => 'الرجاء إدخال إسم العميل',
            'clientName.unique' => 'هذا العميل موجود مسبقاً'
        ];
    }


    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Client::create(['clientName' => $this->clientName, 'phone' => $this->phone, 'initialBalance'=> floatval($this->initialBalance), 'currentBalance'=> floatval($this->initialBalance)]);
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
    public function render()
    {
        $this->clients = \App\Models\Client::where('clientName', 'like', '%' . $this->search . '%')->orWhere('phone', 'like', '%' . $this->search . '%')->get();
        return view('livewire.client');
    }
}
