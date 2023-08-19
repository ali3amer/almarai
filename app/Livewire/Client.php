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
    public string $name = '';
    #[Rule('required|min:2')]
    public string $phone = '';
    public string $search = '';
    public Collection $clients;

    public function save($id)
    {

        if ($this->validate()) {
            if ($this->id == 0) {
                \App\Models\Client::create(['name' => $this->name, 'phone' => $this->phone]);
            } else {
                $client = \App\Models\Client::find($id);
                $client->name = $this->name;
                $client->phone = $this->phone;
                $client->save();
            }
            $this->id = 0;
            $this->name = '';
            $this->phone = '';
        }

    }

    public function edit($client)
    {
        $this->id = $client['id'];
        $this->name = $client['name'];
        $this->phone = $client['phone'];
    }

    public function delete($id)
    {
        $client = \App\Models\Client::find($id);
        $client->delete();
    }
    public function render()
    {
        $this->clients = \App\Models\Client::where('name', 'like', '%' . $this->search . '%')->orWhere('phone', 'like', '%' . $this->search . '%')->get();
        return view('livewire.client');
    }
}
