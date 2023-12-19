<?php

namespace App\Livewire;

use Livewire\Component;

class Navbar extends Component
{
    public $permissions = [];
    public $username;
    public function mount()
    {
        $this->permissions = auth()->user()->allPermissions()->keyBy("name")->toArray();
        $this->username = auth()->user()->name;
    }
    public function render()
    {
        return view('livewire.navbar');
    }
}
