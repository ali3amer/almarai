<?php

namespace App\Livewire;

use App\Models\PurchaseReturn;
use App\Models\SaleReturn;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Invoice extends Component
{
    public array $invoice = [];
    public Collection $returns;
    public Setting $settings;
    public $name = true;
    #[On('sale_created')]
    public function printInvoice($invoice)
    {

        $this->settings = Setting::first();
        $this->invoice = [];
        $this->invoice = $invoice;
        if ($this->invoice['type'] == "sale") {
            $this->returns = SaleReturn::where("sale_id", $this->invoice['id'])->get();
        } elseif ($this->invoice['type'] == "purchase") {
            $this->returns = PurchaseReturn::where("purchase_id", $this->invoice['id'])->get();
        }

    }
    public function render()
    {
        return view('livewire.invoice');
    }
}
