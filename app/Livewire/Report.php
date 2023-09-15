<?php

namespace App\Livewire;

use App\Models\PurchaseDebt;
use App\Models\PurchaseDetail;
use App\Models\SaleDebt;
use App\Models\SaleDetail;
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
        'general' => 'تقرير عام',
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
    public Collection $purchaseDebts;
    public collection $clients;
    public collection $suppliers;
    public collection $products;
    public string $clientSearch = '';
    public string $supplierSearch = '';
    public float $salesSum = 0;
    public float $purchasesSum = 0;
    public float $expensesSum = 0;
    public float $employeesSum = 0;

    public function chooseClient($client)
    {
        $this->currentClient = $client;
    }

    public function chooseSupplier($supplier)
    {
        $this->currentSupplier = $supplier;
    }
    public function chooseReport()
    {
        if ($this->reportType == 0) {
            $this->reset();
        } elseif ($this->reportType == 'general') {
            if ($this->reportDuration == 'day') {
                $this->salesSum = SaleDebt::where('due_date', $this->day)->sum('paid');
                $this->purchasesSum = PurchaseDebt::where('due_date', $this->day)->sum('paid');
                $this->expensesSum = \App\Models\Expense::where('expense_date', $this->day)->sum('amount');
                $this->employeesSum = \App\Models\EmployeeGift::where('gift_date', $this->day)->sum('gift_amount');
            } elseif ($this->reportDuration == 'duration') {
                $this->salesSum = SaleDebt::whereBetween('due_date', [$this->from, $this->to])->sum('paid');
                $this->purchasesSum = PurchaseDebt::whereBetween('due_date', [$this->from, $this->to])->sum('paid');
                $this->expensesSum = \App\Models\Expense::whereBetween('expense_date', [$this->from, $this->to])->sum('amount');
                $this->employeesSum = \App\Models\EmployeeGift::whereBetween('gift_date', [$this->from, $this->to])->sum('gift_amount');

            }
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
                $this->saleDebts = SaleDebt::join('sales', 'sales.id', '=', 'sale_debts.sale_id')->where('sales.client_id', $this->currentClient['id'])->where('sale_debts.due_date', $this->day)->get();
            } elseif ($this->reportDuration == 'duration') {
                $this->saleDebts = SaleDebt::join('sales', 'sales.id', '=', 'sale_debts.sale_id')->where('sales.client_id', $this->currentClient['id'])->whereBetween('sale_debts.due_date', [$this->from, $this->to])->get();
            }
        } elseif ($this->reportType == 'supplier') {   // supplier
            if ($this->reportDuration == 'day') {
                $this->purchaseDebts = PurchaseDebt::join('purchases', 'purchases.id', '=', 'purchase_debts.purchase_id')->where('purchases.supplier_id', $this->currentSupplier['id'])->where('purchase_debts.due_date', $this->day)->get();
            } elseif ($this->reportDuration == 'duration') {
                $this->purchaseDebts = PurchaseDebt::join('purchases', 'purchases.id', '=', 'purchase_debts.purchase_id')->where('purchases.supplier_id', $this->currentSupplier['id'])->whereBetween('sale_debts.due_date', [$this->from, $this->to])->get();
            }
        } elseif ($this->reportType == 'safe') {   // safe
            if ($this->reportDuration == 'day') {
            } elseif ($this->reportDuration == 'duration') {
            }
        } elseif ($this->reportType == 'sales') {  // sale
            if ($this->reportDuration == 'day') {
                $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')->select('sale_details.*', 'sales.sale_date')->with('product', 'sale.client')->where('sales.sale_date', $this->day)->get();
            } elseif ($this->reportDuration == 'duration') {
                $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')->select('sale_details.*', 'sales.sale_date')->with('product', 'sale.client')->whereBetween('sales.sale_date', [$this->from, $this->to])->get();
            }
        } elseif ($this->reportType == 'purchases') {  // purchase
            if ($this->reportDuration == 'day') {
                $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')->select('purchase_details.*', 'purchases.purchase_date')->with('product', 'purchase.supplier')->where('purchases.purchase_date', $this->day)->get();
            } elseif ($this->reportDuration == 'duration') {
                $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')->select('purchase_details.*', 'purchases.purchase_date')->with('product', 'purchase.supplier')->whereBetween('purchases.purchase_date', [$this->from, $this->to])->get();
            }
        }
    }

    public function render()
    {
        $this->stores = \App\Models\Store::all();
        if ($this->reportType == 'client') {
            $this->clients = \App\Models\Client::where('clientName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        } elseif ($this->reportType == 'supplier') {
            $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();
        }
        return view('livewire.report');
    }
}
