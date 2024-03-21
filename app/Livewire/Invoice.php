<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Attributes\On;
use Livewire\Component;

class Invoice extends Component
{
    public array $invoice = [];

    public Setting $settings;
    public $name = true;
    #[On('sale_created')]
    public function printInvoice($invoice)
    {
        $this->settings = Setting::first();
        $this->invoice = [];
        $this->invoice = $invoice;
    }
    public function render()
    {
        return view('livewire.invoice');
    }
}
