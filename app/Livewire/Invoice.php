<?php

namespace App\Livewire;

use App\Models\PurchaseReturn;
use App\Models\SaleReturn;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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
        foreach ($invoice['cart'] as $key => $item) {
            if (isset($item['sale_id']) || isset($item['purchase_id'])) {
                if ($invoice['type'] == "sale") {
                    $return = SaleReturn::where("sale_id", $item['sale_id'])
                        ->where("product_id", $item['product_id'])
                        ->select(
                            DB::raw("SUM(amount) as total_amount"),
                            DB::raw("SUM(quantity) as total_quantity")
                        )
                        ->first();
                } else {
                    $return = PurchaseReturn::where("purchase_id", $item['purchase_id'])
                        ->where("product_id", $item['product_id'])
                        ->select(
                            DB::raw("SUM(amount) as total_amount"),
                            DB::raw("SUM(quantity) as total_quantity")
                        )
                        ->first();
                }
                $invoice['cart'][$key]['quantity'] -= $return->total_quantity;
                $invoice['cost'] -= $return->total_quantity * $invoice['cart'][$key]['price'];
                $invoice['paid'] -= $return->total_amount;
            }
        }

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
