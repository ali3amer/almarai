<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class Invoice extends Component
{
    public array $invoice = [];

    #[On('sale_created')]
    public function printInvoice($invoice)
    {
        $this->invoice = $invoice;
    }
    public function render()
    {
        return view('livewire.invoice');
    }
}
