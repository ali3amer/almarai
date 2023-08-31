<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Client extends Component
{
    public string $title = 'العملاء';
    public int $id = 0;
    #[Rule('required|min:2')]
    public string $clientName = '';
    #[Rule('required|min:2')]
    public string $phone = '';
    public string $search = '';
    public Collection $clients;

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Client::create(['clientName' => $this->clientName, 'phone' => $this->phone]);
            } else {
                $client = \App\Models\Client::find($id);
                $client->clientName = $this->clientName;
                $client->phone = $this->phone;
                $client->save();
            }
            $this->id = 0;
            $this->clientName = '';
            $this->phone = '';
        }

    }

    public function edit($client)
    {
        $this->id = $client['id'];
        $this->clientName = $client['clientName'];
        $this->phone = $client['phone'];
    }

    public function delete($id)
    {
        $client = \App\Models\Client::find($id);
        $client->delete();
    }
    public function render()
    {
        $this->clients = \App\Models\Client::where('clientName', 'like', '%' . $this->search . '%')->orWhere('phone', 'like', '%' . $this->search . '%')->get();
        return view('livewire.client');
    }
}
