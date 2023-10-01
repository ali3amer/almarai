<?php

namespace App\Livewire;

use App\Models\ClientDebt;
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
    public array $currentProduct = [];
    public array $cart = [];

    public array $reportTypes = [
        0 => '-------------------------',
        'general' => 'تقرير عام',
        'inventory' => 'تقرير جرد',
        'client' => 'تقرير عميل',
        'supplier' => 'تقرير مورد',
//        'safe' => 'تقرير خزنة',
        'sales' => 'تقرير مبيعات',
        'purchases' => 'تقرير مشتريات',
//        'category' => 'تقرير قسم معين',
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
    public collection $debts;
    public collection $pays;
    public collection $suppliers;
    public collection $products;
    public string $clientSearch = '';
    public string $supplierSearch = '';
    public float $salesSum = 0;
    public float $debtsSum = 0;
    public float $paysSum = 0;
    public float $purchasesSum = 0;
    public float $expensesSum = 0;
    public float $employeesSum = 0;
    public float $damagedsSum = 0;
    public float $percent = 0;
    public string $productSearch = '';

    public function chooseClient($client)
    {
        $this->currentClient = $client;
    }

    public function chooseSupplier($supplier)
    {
        $this->currentSupplier = $supplier;
    }

    public function chooseProduct($supplier)
    {
        $this->currentProduct = $supplier;
    }


    public function chooseReport()
    {
        $this->resetData();
        if ($this->reportType == 'general') {
            if ($this->reportDuration == 'day') {
                $this->salesSum = SaleDebt::where('due_date', $this->day)->sum('paid');
                $this->purchasesSum = PurchaseDebt::where('due_date', $this->day)->sum('paid');
                $this->expensesSum = \App\Models\Expense::where('expense_date', $this->day)->sum('amount');
                $this->employeesSum = \App\Models\EmployeeGift::where('gift_date', $this->day)->sum('gift_amount');
                $this->paysSum = \App\Models\ClientDebt::where('due_date', $this->day)->where('type', 'pay')->sum('debt_amount');
                $this->debtsSum = \App\Models\ClientDebt::where('due_date', $this->day)->where('type', 'debt')->sum('debt_amount');
                if (\App\Models\Damaged::where('damaged_date', $this->day)->count() > 0) {
                    $this->damagedsSum = \App\Models\Damaged::where('damaged_date', $this->day)->join('products', 'damageds.product_id', '=', 'products.id')
                        ->select(DB::raw('SUM(damageds.quantity * products.purchase_price) AS total_damage_cost'))
                        ->groupBy('damageds.product_id')->first()->total_damage_cost;
                }


            } elseif ($this->reportDuration == 'duration') {
                $this->salesSum = SaleDebt::whereBetween('due_date', [$this->from, $this->to])->sum('paid');
                $this->purchasesSum = PurchaseDebt::whereBetween('due_date', [$this->from, $this->to])->sum('paid');
                $this->expensesSum = \App\Models\Expense::whereBetween('expense_date', [$this->from, $this->to])->sum('amount');
                $this->employeesSum = \App\Models\EmployeeGift::whereBetween('gift_date', [$this->from, $this->to])->sum('gift_amount');

                $this->paysSum = \App\Models\ClientDebt::whereBetween('due_date', [$this->from, $this->to])->where('type', 'pay')->sum('debt_amount');
                $this->debtsSum = \App\Models\ClientDebt::whereBetween('due_date', [$this->from, $this->to])->where('type', 'debt')->sum('debt_amount');

                if (\App\Models\Damaged::whereBetween('damaged_date', [$this->from, $this->to])->count() > 0) {
                    $this->damagedsSum = \App\Models\Damaged::whereBetween('damaged_date', [$this->from, $this->to])->join('products', 'damageds.product_id', '=', 'products.id')
                        ->select(DB::raw('SUM(damageds.quantity * products.purchase_price) AS total_damage_cost'))
                        ->groupBy('damageds.product_id')->first()->total_damage_cost;
                }
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
                $this->debts = ClientDebt::where('client_id', $this->currentClient['id'])->where('due_date', $this->day)->get();
            } elseif ($this->reportDuration == 'duration') {
                $this->saleDebts = SaleDebt::join('sales', 'sales.id', '=', 'sale_debts.sale_id')->where('sales.client_id', $this->currentClient['id'])->whereBetween('sale_debts.due_date', [$this->from, $this->to])->get();
                $this->debts = ClientDebt::where('client_id', $this->currentClient['id'])->whereBetween('due_date', [$this->from, $this->to])->get();

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
                if ($this->store_id != 0 && empty($this->currentProduct)) {
                    $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')
                        ->join('products', 'products.id', '=', 'sale_details.product_id')
                        ->join('stores', 'stores.id', '=', 'products.store_id')
                        ->select('sale_details.*', 'sales.sale_date')
                        ->with('product', 'sale.client')
                        ->where('sales.sale_date', $this->day)->where('products.store_id', $this->store_id)
                        ->get();
                } elseif (!empty($this->currentProduct)) {
                    $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')
                        ->join('products', 'products.id', '=', 'sale_details.product_id')
                        ->join('stores', 'stores.id', '=', 'products.store_id')
                        ->select('sale_details.*', 'sales.sale_date')
                        ->with('product', 'sale.client')
                        ->where('sales.sale_date', $this->day)->where('products.id', $this->currentProduct['id'])
                        ->get();
                } else {
                    $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')
                        ->select('sale_details.*', 'sales.sale_date')
                        ->with('product', 'sale.client')
                        ->where('sales.sale_date', $this->day)
                        ->get();
                }
            } elseif ($this->reportDuration == 'duration') {

                if ($this->store_id != 0 && empty($this->currentProduct)) {
                    $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')
                        ->join('products', 'products.id', '=', 'sale_details.product_id')
                        ->join('stores', 'stores.id', '=', 'products.store_id')
                        ->select('sale_details.*', 'sales.sale_date')
                        ->with('product', 'sale.client')
                        ->whereBetween('sales.sale_date', [$this->from, $this->to])->where('products.store_id', $this->store_id)
                        ->get();
                } elseif (!empty($this->currentProduct)) {
                    $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')
                        ->join('products', 'products.id', '=', 'sale_details.product_id')
                        ->join('stores', 'stores.id', '=', 'products.store_id')
                        ->select('sale_details.*', 'sales.sale_date')
                        ->with('product', 'sale.client')
                        ->whereBetween('sales.sale_date', [$this->from, $this->to])->where('products.id', $this->currentProduct['id'])
                        ->get();
                } else {
                    $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')
                        ->select('sale_details.*', 'sales.sale_date')
                        ->with('product', 'sale.client')
                        ->whereBetween('sales.sale_date', [$this->from, $this->to])
                        ->get();
                }
            }

            $this->sum = 0;
            foreach ($this->sales as $sale) {
                $this->sum += $sale->quantity * $sale->price;
            }

        } elseif ($this->reportType == 'purchases') {  // purchase
            if ($this->reportDuration == 'day') {
                if ($this->store_id != 0 && empty($this->currentProduct)) {
                    $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
                        ->join('products', 'products.id', '=', 'purchase_details.product_id')
                        ->join('stores', 'stores.id', '=', 'products.store_id')
                        ->select('purchase_details.*', 'purchases.purchase_date')
                        ->with('product', 'purchase.supplier')
                        ->where('purchases.purchase_date', $this->day)->where('products.store_id', $this->store_id)
                        ->get();
                } elseif (!empty($this->currentProduct)) {
                    $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
                        ->join('products', 'products.id', '=', 'purchase_details.product_id')
                        ->join('stores', 'stores.id', '=', 'products.store_id')
                        ->select('purchase_details.*', 'purchases.purchase_date')
                        ->with('product', 'purchase.supplier')
                        ->where('purchases.purchase_date', $this->day)->where('products.id', $this->currentProduct['id'])
                        ->get();
                } else {
                    $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
                        ->select('purchase_details.*', 'purchases.purchase_date')
                        ->with('product', 'purchase.supplier')
                        ->where('purchases.purchase_date', $this->day)
                        ->get();
                }
            } elseif ($this->reportDuration == 'duration') {

                if ($this->store_id != 0 && empty($this->currentProduct)) {
                    $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
                        ->join('products', 'products.id', '=', 'purchase_details.product_id')
                        ->join('stores', 'stores.id', '=', 'products.store_id')
                        ->select('purchase_details.*', 'purchases.purchase_date')
                        ->with('product', 'purchase.supplier')
                        ->whereBetween('purchases.purchase_date', [$this->from, $this->to])->where('products.store_id', $this->store_id)
                        ->get();
                } elseif (!empty($this->currentProduct)) {
                    $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
                        ->join('products', 'products.id', '=', 'purchase_details.product_id')
                        ->join('stores', 'stores.id', '=', 'products.store_id')
                        ->select('purchase_details.*', 'purchases.purchase_date')
                        ->with('product', 'purchase.supplier')
                        ->whereBetween('purchases.purchase_date', [$this->from, $this->to])->where('products.id', $this->currentProduct['id'])
                        ->get();
                } else {
                    $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
                        ->select('purchase_details.*', 'purchases.purchase_date')
                        ->with('product', 'purchase.supplier')
                        ->whereBetween('purchases.purchase_date', [$this->from, $this->to])
                        ->get();
                }
            }
            $this->sum = 0;
            foreach ($this->purchases as $purchase) {
                $this->sum += $purchase->quantity * $purchase->price;
            }
        }
    }

    public function resetData() {
        $this->reset('sum', 'salesSum','debtsSum','paysSum','purchasesSum','expensesSum','employeesSum','damagedsSum', 'percent');
    }

    public function render()
    {
        $this->stores = \App\Models\Store::all();
        if ($this->reportType == 'inventory') {
            $this->reportDuration = $this->reportDurations[0];
        }
        if ($this->reportType == 'client') {
            $this->clients = \App\Models\Client::where('clientName', 'LIKE', '%' . $this->clientSearch . '%')->get();
        } elseif ($this->reportType == 'supplier') {
            $this->suppliers = \App\Models\Supplier::where('supplierName', 'LIKE', '%' . $this->supplierSearch . '%')->get();
        } elseif ($this->reportType == 'sales' || $this->reportType == 'purchases') {
            if ($this->store_id == 0) {
                $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->get();
            } else {
                $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->where('store_id', $this->store_id)->get();
            }
        }
        return view('livewire.report');
    }
}
