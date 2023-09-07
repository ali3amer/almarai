<?php

namespace App\Livewire;

use App\Models\SaleDebt;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Report extends Component
{

    public string $title = 'التقارير';

    public int $reportType = 0;
    public string $day = '';
    public string $from = '';
    public string $to = '';
    public int $reportDuration = 0;

    public array $currentClient = [];
    public array $currentSupplier = [];
    public array $cart = [];

    public array $reportTypes = [
        0 => '-------------------------',
        1 => 'تقرير عام',
        2 => 'تقرير عميل',
        3 => 'تقرير مورد',
        4 => 'تقرير خزنة',
        5 => 'تقرير مبيعات',
        6 => 'تقرير مشتريات',
    ];
    public array $reportDurations = [
        0 => '-------------------------',
        1 => 'تقرير يوميه',
        2 => 'تقرير فتره',
    ];
    public string $search = '';

    public collection $purchases;
    public collection $sales;
    public collection $saleDebts;
    public collection $clients;
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
        } elseif ($this->reportType == 1) {
            if ($this->reportDuration == 1) {

            } elseif ($this->reportDuration == 2) {
            }
        } elseif ($this->reportType == 2) { // client
            if ($this->reportDuration == 1) {
                $this->sales = \App\Models\Sale::with( 'client','saleDetails.product', 'saleDebts')->where('client_id', $this->currentClient['id'])->where('sale_date', $this->day)->get();
                $this->saleDebts = SaleDebt::join('sales', 'sale_debts.sale_id', '=', 'sales.id')
                    ->where('sales.client_id', '=', $this->currentClient['id'])->select('sale_debts.*', 'sales.total_amount')
                    ->get();
                dd($this->saleDebts);
            } elseif ($this->reportDuration == 2) {
                $this->sales = \App\Models\Sale::with('client', 'saleDetails.product', 'saleDebts')->where('client_id', $this->currentClient['id'])->whereBetween('sale_date', [$this->from, $this->to])->get();
            }
        } elseif ($this->reportType == 3) {   // supplier
            if ($this->reportDuration == 1) {
            } elseif ($this->reportDuration == 2) {
            }
        } elseif ($this->reportType == 4) {   // safe
            if ($this->reportDuration == 1) {
            } elseif ($this->reportDuration == 2) {
            }
        } elseif ($this->reportType == 5) {  // sale
            if ($this->reportDuration == 1) {
            } elseif ($this->reportDuration == 2) {
            }
        } elseif ($this->reportType == 6) {  // purchase
            if ($this->reportDuration == 1) {
            } elseif ($this->reportDuration == 2) {
            }
        }
    }

    public function render()
    {
        $this->clients = \App\Models\Client::where('clientName', 'LIKE', '%'.$this->clientSearch.'%')->get();
        return view('livewire.report');
    }
}
