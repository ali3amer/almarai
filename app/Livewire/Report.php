<?php

namespace App\Livewire;

use App\Models\ClientDebt;
use App\Models\EmployeeDebt;
use App\Models\PurchaseDebt;
use App\Models\PurchaseDetail;
use App\Models\SaleDebt;
use App\Models\SaleDetail;
use App\Models\SupplierDebt;
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
    public collection $debts;
    public collection $pays;
    public collection $suppliers;
    public collection $products;
    public string $clientSearch = '';
    public string $supplierSearch = '';
    public float $safeBalance = 0;
    public float $salesSum = 0;
    public float $debtsSum = 0;
    public float $paysSum = 0;
    public float $purchasesSum = 0;
    public float $expensesSum = 0;
    public float $employeesSum = 0;
    public float $damagedsSum = 0;
    public $percent = 0;
    public string $productSearch = '';
    public array $invoice = [];
    public Collection $clientDebts;
    public Collection $supplierDebts;
    public Collection $employeeDebts;
    public float $clientSaleSum = 0;
    public float $employeeSaleSum = 0;
    public float $supplierSaleSum = 0;
    public float $totalSales = 0;
    public float $totalPurchases = 0;
    public float $salesDebts = 0;
    public float $salesBalance = 0;
    public float $purchasesBalance = 0;
    public float $purchasesDebts = 0;
    public float $total = 0;
    public float $salesPaidSum = 0;
    public float $purchasesPaidSum = 0;
    /**
     * @var float|mixed
     */
    public float $currentSalesBalance = 0;
    public float $currentPurchasesBalance = 0;

    public function chooseClient($client)
    {
        $this->currentSupplier = [];
        $this->currentClient = $client;
    }

    public function chooseSupplier($supplier)
    {
        $this->currentClient = [];
        $this->currentSupplier = $supplier;
    }

    public function chooseProduct($supplier)
    {
        $this->currentProduct = $supplier;
    }


    public function chooseReport()
    {
        if ($this->reportType == 'general') {
            if ($this->reportDuration == 'day') {

                $this->salesSum = \App\Models\Sale::where('sale_date', $this->day)->sum('total_amount');
                $this->salesPaidSum = \App\Models\SaleDebt::where('due_date', $this->day)->where('type', 'pay')->sum('paid');

                $this->purchasesSum = \App\Models\Purchase::where('purchase_date', $this->day)->sum('total_amount');
                $this->purchasesPaidSum = \App\Models\PurchaseDebt::where('due_date', $this->day)->where('type', 'pay')->sum('paid');

                $this->expensesSum = \App\Models\Expense::where('expense_date', $this->day)->sum('amount');
                $this->employeesSum = \App\Models\EmployeeGift::where('gift_date', $this->day)->sum('gift_amount');

                if (\App\Models\Damaged::where('damaged_date', $this->day)->count() > 0) {
                    $this->damagedsSum = \App\Models\Damaged::where('damaged_date', $this->day)->join('products', 'damageds.product_id', '=', 'products.id')
                        ->select(DB::raw('SUM(damageds.quantity * products.purchase_price) AS total_damage_cost'))
                        ->groupBy('damageds.product_id')->first()->total_damage_cost;
                }

            } elseif ($this->reportDuration == 'duration') {

                $this->salesSum = \App\Models\Sale::whereBetween('sale_date', [$this->from, $this->to])->sum('total_amount');
                $this->salesPaidSum = \App\Models\SaleDebt::whereBetween('due_date', [$this->from, $this->to])->where('type', 'pay')->sum('paid');

                $this->purchasesSum = \App\Models\Purchase::whereBetween('purchase_date', [$this->from, $this->to])->sum('total_amount');
                $this->purchasesPaidSum = \App\Models\PurchaseDebt::whereBetween('due_date', [$this->from, $this->to])->where('type', 'pay')->sum('paid');

                $this->expensesSum = \App\Models\Expense::whereBetween('expense_date', [$this->from, $this->to])->sum('amount');
                $this->employeesSum = \App\Models\EmployeeGift::whereBetween('gift_date', [$this->from, $this->to])->sum('gift_amount');

                if (\App\Models\Damaged::whereBetween('damaged_date', [$this->from, $this->to])->count() > 0) {
                    $this->damagedsSum = \App\Models\Damaged::whereBetween('damaged_date', [$this->from, $this->to])->join('products', 'damageds.product_id', '=', 'products.id')
                        ->select(DB::raw('SUM(damageds.quantity * products.purchase_price) AS total_damage_cost'))
                        ->groupBy('damageds.product_id')->first()->total_damage_cost;
                }
            } else {
                $this->salesSum = \App\Models\Sale::sum('total_amount');
                $this->salesPaidSum = \App\Models\SaleDebt::where('type', 'pay')->sum('paid');

                $this->purchasesSum = \App\Models\Purchase::sum('total_amount');
                $this->purchasesPaidSum = \App\Models\PurchaseDebt::where('type', 'pay')->sum('paid');

                $this->expensesSum = \App\Models\Expense::sum('amount');
                $this->employeesSum = \App\Models\EmployeeGift::sum('gift_amount');

                if (\App\Models\Damaged::count() > 0) {
                    $this->damagedsSum = \App\Models\Damaged::join('products', 'damageds.product_id', '=', 'products.id')
                        ->select(DB::raw('SUM(damageds.quantity * products.purchase_price) AS total_damage_cost'))
                        ->groupBy('damageds.product_id')->first()->total_damage_cost;
                }
            }

            $this->salesDebts = $this->salesSum - $this->salesPaidSum;

            $this->purchasesDebts = $this->purchasesSum - $this->purchasesPaidSum;

            $safe = \App\Models\Safe::first()->initialBalance;

            $this->safeBalance = $safe + $this->salesPaidSum - $this->purchasesPaidSum - $this->expensesSum - $this->employeesSum - $this->damagedsSum;

            $this->total = $this->safeBalance + $this->salesDebts - $this->purchasesDebts;

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
                $this->saleDebts = SaleDebt::where('client_id', $this->currentClient['id'])->where('due_date', $this->day)->get();
            } elseif ($this->reportDuration == 'duration') {
                $this->saleDebts = SaleDebt::where('client_id', $this->currentClient['id'])->whereBetween('due_date', [$this->from, $this->to])->get();
            } else {
                $this->saleDebts = SaleDebt::where('client_id', $this->currentClient['id'])->get();
            }
            $this->salesBalance = $this->currentClient['initialBalance'] + $this->saleDebts->sum('debt') - $this->saleDebts->sum('paid');
            $this->currentSalesBalance = $this->salesBalance;
        } elseif ($this->reportType == 'supplier') {   // supplier
            if ($this->reportDuration == 'day') {
                $this->saleDebts = \App\Models\SaleDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', $this->day)->get();
                $this->purchaseDebts = \App\Models\PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', $this->day)->get();
            } elseif ($this->reportDuration == 'duration') {
                $this->saleDebts = \App\Models\SaleDebt::where('supplier_id', $this->currentSupplier['id'])->whereBetween('due_date', [$this->from, $this->to])->get();
                $this->purchaseDebts = \App\Models\PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->whereBetween('due_date', [$this->from, $this->to])->get();
            } else {
                $this->saleDebts = \App\Models\SaleDebt::where('supplier_id', $this->currentSupplier['id'])->get();
                $this->purchaseDebts = \App\Models\PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->get();
            }

            $this->salesBalance = $this->currentSupplier['initialSalesBalance'] + $this->saleDebts->sum('debt') - $this->saleDebts->sum('paid');

            $this->purchasesBalance = $this->currentSupplier['initialBalance'] + $this->purchaseDebts->sum('debt') - $this->purchaseDebts->sum('paid');

            $this->currentSalesBalance = $this->salesBalance;

            $this->currentPurchasesBalance = $this->purchasesBalance;


        } elseif ($this->reportType == 'sales') {  // sale
            if ($this->reportDuration == 'day') {
                if (!empty($this->currentProduct)) {
                    $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')->where('sales.sale_date', $this->day)->where('product_id', $this->currentProduct['id'])->get();
                } else {
                    $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')->where('sales.sale_date', $this->day)->get();
                }
            } elseif ($this->reportDuration == 'duration') {

                if (!empty($this->currentProduct)) {
                    $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')->whereBetween('sales.sale_date', [$this->from, $this->to])->where('product_id', $this->currentProduct['id'])->get();
                } else {
                    $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')->whereBetween('sales.sale_date', [$this->from, $this->to])->get();
                }
            } else {
                if (!empty($this->currentProduct)) {
                    $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')->where('product_id', $this->currentProduct['id'])->get();
                } else {
                    $this->sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')->get();
                }
            }

            $this->sum = 0;
            foreach ($this->sales as $sale) {
                $this->sum += $sale->quantity * $sale->price;
            }

        } elseif ($this->reportType == 'purchases') {  // purchase
            if ($this->reportDuration == 'day') {
                if (!empty($this->currentProduct)) {
                    $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')->where('purchases.purchase_date', $this->day)->where('product_id', $this->currentProduct['id'])->get();
                } else {
                    $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')->where('purchases.purchase_date', $this->day)->get();
                }
            } elseif ($this->reportDuration == 'duration') {

                if (!empty($this->currentProduct)) {
                    $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')->whereBetween('purchases.purchase_date', [$this->from, $this->to])->where('product_id', $this->currentProduct['id'])->get();
                } else {
                    $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')->whereBetween('purchases.purchase_date', [$this->from, $this->to])->get();
                }
            } else {
                if (!empty($this->currentProduct)) {
                    $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')->where('product_id', $this->currentProduct['id'])->get();
                } else {
                    $this->purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')->get();
                }
            }
            $this->sum = 0;
            foreach ($this->purchases as $purchase) {
                $this->sum += $purchase->quantity * $purchase->price;
            }
        }
    }

    public function getInvoice($debt)
    {
        if (isset($debt['sale_id']) || isset($debt['purchase_id'])) {
            $type = isset($debt['sale_id']) ? 'sale' : 'purchase';
            $this->invoice['id'] = $debt[$type . '_id'];
            $this->invoice['type'] = $type;
            $this->invoice['date'] = $debt['due_date'];
            if ($this->reportType == 'client') {
                $this->invoice['client'] = $this->currentClient['clientName'];
            } elseif ($this->reportType == 'supplier') {
                $this->invoice['client'] = $this->currentSupplier['supplierName'];
            }
            $this->invoice['clientType'] = $this->reportType == 'supplier' ? 'المورد' : 'العميل';
            if ($type == 'sale') {
                $this->invoice['cart'] = SaleDetail::where('sale_id', $this->invoice['id'])->join('products', 'products.id', '=', 'sale_details.product_id')->get()->toArray();
            } else {
                $this->invoice['cart'] = PurchaseDetail::where('purchase_id', $this->invoice['id'])->join('products', 'products.id', '=', 'purchase_details.product_id')->get()->toArray();
            }
            $this->invoice['total_amount'] = $debt['debt'];
            $this->invoice['showMode'] = true;
            $this->dispatch('sale_created', $this->invoice);
        }
    }

    public function resetData()
    {
        $this->reset('sum', 'salesSum', 'currentClient', 'currentSupplier', 'debtsSum', 'paysSum', 'purchasesSum', 'expensesSum', 'employeesSum', 'damagedsSum', 'percent');
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
