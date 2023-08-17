<?php

namespace App\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public int $count = 1;
    public string $title;

    public function mount(string $title = 'Counter')
    {
        $this->title = $title;
    }

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }
    public function render()
    {
        return view('livewire.counter');
    }
}
