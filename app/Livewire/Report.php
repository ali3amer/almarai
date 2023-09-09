<?php

namespace App\Livewire;

use App\Models\SaleDebt;
use Illuminate\Contracts\Database\Eloquent\Builder;

;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use function Livewire\store;

class Report extends Component
{

    public string $title = 'التقارير';

    public string $reportType = '';
    public int $store_id = 0;
    public float $sum = 0;
    public string $day = '';
    public string $from = '';
    public string $to = '';
    public string $reportDuration = '';

    public array $currentClient = [];
    public array $currentSupplier = [];
    public array $cart = [];

    public array $reportTypes = [
        0 => '-------------------------',
        'inventory' => 'تقرير جرد',
        'client' => 'تقرير عميل',
        'supplier' => 'تقرير مورد',
        'safe' => 'تقرير خزنة',
        'sales' => 'تقرير مبيعات',
        'purchases' => 'تقرير مشتريات',
    ];
    public array $reportDurations = [
        0 => '-------------------------',
        'day' => 'تقرير يوميه',
        'duration' => 'تقرير فتره',
    ];
    public string $search = '';

    public collection $purchases;
    public collection $sales;
    public collection $stores;
    public collection $saleDebts;
    public collection $clients;
    public collection $products;
    public collection $suppliers;
    public string $clientSearch = '';

    public function chooseClient($client)
    {
        $this->currentClient = $client;
    }

    public function chooseReport()
    {
        if ($this->reportType == 0) {
            $this->reset();
        } elseif ($this->reportType == 'inventory') {
            if ($this->store_id == 0) {
                $this->products = \App\Models\Product::all();
            } else {
                $this->products = \App\Models\Product::where('store_id', $this->store_id)->get();
            }

            foreach ($this->products as $product) {
                $this->sum += $product->stock * $product->purchase_price;
            }
        } elseif ($this->reportType == 'client') { // client
            if ($this->reportDuration == 'day') {
//                $sales = \App\Models\Sale::where('client_id', $this->currentClient['id'])->where('sale_date', $this->day)->get()->keyBy('id')->toArray();
//                $keys = array_keys($sales);
                $sale_debts = SaleDebt::join('sales', 'sales.id', '=', 'sale_debts.sale_id')->where('sales.client_id', $this->currentClient['id'])->where('due_date', $this->day)->select('sale_debts.*', 'sales.created_at as sale_at', 'sales.sale_date')->get();
                dd($sale_debts);

                $combined = [];
                foreach ($sales as $sale) {
                    $combined[] = $sale;
                }
                foreach ($sale_debts as $sale_debt) {
                    $combined[] = $sale_debt;
                }

                usort($combined, function ($a, $b) {
                    return $a->created_at <=> $b->created_at;
                });

                dd($combined);
            } elseif ($this->reportDuration == 'duration') {
                $this->sales = \App\Models\Sale::with('client', 'saleDetails.product', 'saleDebts')->where('client_id', $this->currentClient['id'])->whereBetween('sale_date', [$this->from, $this->to])->get();
            }
        } elseif ($this->reportType == 'supplier') {   // supplier
            if ($this->reportDuration == 'day') {
            } elseif ($this->reportDuration == 'duration') {
            }
        } elseif ($this->reportType == 'safe') {   // safe
            if ($this->reportDuration == 'day') {
            } elseif ($this->reportDuration == 'duration') {
            }
        } elseif ($this->reportType == 'sales') {  // sale
            if ($this->reportDuration == 'day') {
            } elseif ($this->reportDuration == 'duration') {
            }
        } elseif ($this->reportType == 'purchases') {  // purchase
            if ($this->reportDuration == 'day') {
            } elseif ($this->reportDuration == 'duration') {
            }
        }
    }

    public function render()
    {
        $this->stores = \App\Models\Store::all();
        $this->clients = \App\Models\Client::where('clientName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        return view('livewire.report');
    }
}
