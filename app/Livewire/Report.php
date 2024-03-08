<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\ClientDebt;
use App\Models\EmployeeDebt;
use App\Models\EmployeeGift;
use App\Models\Expense;
use App\Models\PurchaseDebt;
use App\Models\PurchaseDetail;
use App\Models\Safe;
use App\Models\SaleDebt;
use App\Models\SaleDetail;
use App\Models\SupplierDebt;
use App\Models\Transfer;
use App\Models\Withdraw;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;


;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use function Livewire\store;

class Report extends Component
{
    use LivewireAlert;

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
    public array $currentEmployee = [];
    public array $cart = [];

    public array $reportTypes = [
        0 => '-------------------------',
        'general' => 'تقرير عام',
        'inventory' => 'تقرير جرد',
        'client' => 'تقرير عميل',
        'employee' => 'تقرير موظف',
        'supplier' => 'تقرير مورد',
        'sales' => 'تقرير مبيعات',
        'purchases' => 'تقرير مشتريات',
        'tracking' => 'تقرير متابعة منتج',
        'daily' => 'تقرير القيود اليومية',
        'safe' => 'تقرير خزنة',
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
    public collection $employees;
    public collection $products;
    public string $payment = '';
    public string $clientSearch = '';
    public string $supplierSearch = '';
    public string $employeeSearch = '';
    public float $sale = 0;
    public float $purchase = 0;
    public float $paid = 0;
    public float $debt = 0;
    public float $saleFuture = 0;
    public float $purchaseFuture = 0;
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
    public float $deposits = 0;
    public float $creditors = 0;
    public float $owe = 0;
    public float $salesPaidSum = 0;
    public float $purchasesPaidSum = 0;
    /**
     * @var float|mixed
     */
    public float $currentSalesBalance = 0;
    public float $currentPurchasesBalance = 0;
    public array $merged = [];
    public Collection $transfers;
    public Collection $expenses;
    public Collection $employeeGifts;
    /**
     * @var array|mixed
     */
    public array $array = [];
    public $balance = 0;
    public $quantity = 0;
    public $stock;
    public $bankBalance;
    public Collection $withdraws;
    public $capital = 0;
    public $totalExpenses = 0;
    public $assets = 0;
    public $adversaries = 0;

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

    public function chooseEmployee($employee)
    {
        $this->currentEmployee = [];
        $this->currentEmployee = $employee;
    }

    public function chooseProduct(\App\Models\Product $product)
    {
        $this->currentProduct = $product->toArray();
        $this->currentProduct['stock'] = $product->stock;
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

            $this->stock = 0;

            $products = \App\Models\Product::all();
            foreach ($products as $product) {
                $this->stock += $product->stock * $product->purchase_price;
            }

            $this->capital = Safe::first()->capital ?? 0;

            $this->bankBalance = session('bankBalance');

            $this->balance = Safe::first()->currentBalance ?? 0;
            $this->totalExpenses = Expense::sum("amount") + EmployeeGift::sum("gift_amount");
            $this->clients = \App\Models\Client::get();

            $sum = 0;
            $this->deposits = 0;
            $this->owe = 0;
            foreach ($this->clients as $client) {
                $sum = $client->initialBalance + $client->debts->sum("debt") - $client->debts->sum("paid");
                if ($sum < 0) {
                    $this->deposits += -1 * $sum;
                } else {
                    $this->owe += $sum;
                }
            }

            $this->suppliers = \App\Models\Supplier::get();

            $sum = 0;
            $purchaseSum = 0;
            $this->creditors = 0;
            foreach ($this->suppliers as $supplier) {
                $purchaseSum += $supplier->initialBalance + $supplier->purchaseDebts->sum("debt") - $supplier->purchaseDebts->sum("paid");
                $sum -= $supplier->initialSalesBalance + $supplier->saleDebts->sum("debt") - $supplier->saleDebts->sum("paid");

                if ($purchaseSum > 0) {
                    $this->creditors += $purchaseSum;
                } else {
                    $this->deposits += -1 * $purchaseSum;
                }

                if ($sum > 0) {
                    $this->owe += $sum;
                } else {
                    $this->deposits += -1 * $sum;
                }

            }

            $this->employees = \App\Models\Employee::get();

            foreach ($this->employees as $employee) {
                $sum = $employee->initialBalance + $employee->debts->sum("debt") - $employee->debts->sum("paid");
                if ($sum < 0) {
                    $this->deposits += -1 * $sum;
                } else {
                    $this->owe += $sum;
                }
            }

            $this->assets = $this->stock + $this->bankBalance + $this->balance + $this->totalSales + $this->owe;
            $this->adversaries = $this->capital + $this->deposits + $this->creditors;

            $this->salesDebts = $this->salesSum - $this->salesPaidSum;

            $this->purchasesDebts = $this->purchasesSum - $this->purchasesPaidSum;


            $this->safeBalance = Safe::sum("initialBalance") + $this->salesPaidSum - $this->purchasesPaidSum - $this->expensesSum - $this->employeesSum - $this->damagedsSum;

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
            $this->currentClient['initialBalance'] = \App\Models\Client::find($this->currentClient['id'])->initialBalance;
            if ($this->reportDuration == 'day') {
                $this->currentClient['initialBalance'] += SaleDebt::where('client_id', $this->currentClient['id'])->where('due_date', '<', $this->day)->sum('debt')
                    - SaleDebt::where('client_id', $this->currentClient['id'])->where('due_date', '<', $this->day)->sum('paid')
                    - SaleDebt::where('client_id', $this->currentClient['id'])->where('due_date', '<', $this->day)->sum('discount');

                $this->saleDebts = SaleDebt::where('client_id', $this->currentClient['id'])->where('due_date', $this->day)->get();

            } elseif ($this->reportDuration == 'duration') {
                $this->currentClient['initialBalance'] += SaleDebt::where('client_id', $this->currentClient['id'])->where('due_date', '<', $this->from)->sum('debt')
                    - SaleDebt::where('client_id', $this->currentClient['id'])->where('due_date', '<', $this->from)->sum('paid')
                    - SaleDebt::where('client_id', $this->currentClient['id'])->where('due_date', '<', $this->from)->sum('discount');

                $this->saleDebts = SaleDebt::where('client_id', $this->currentClient['id'])->whereBetween('due_date', [$this->from, $this->to])->orderBy('due_date')->get();

            } else {
                $this->saleDebts = SaleDebt::where('client_id', $this->currentClient['id'])->orderBy('due_date')->get();
            }
            $this->salesBalance = $this->currentClient['initialBalance'] + $this->saleDebts->sum('debt') - $this->saleDebts->sum('paid') - $this->saleDebts->sum('discount');

            $this->currentSalesBalance = $this->salesBalance;
        } elseif ($this->reportType == 'supplier') {   // supplier
            $this->currentSupplier['initialBalance'] = \App\Models\Supplier::find($this->currentSupplier['id'])->initialBalance;
            $this->currentSupplier['initialSalesBalance'] = \App\Models\Supplier::find($this->currentSupplier['id'])->initialSalesBalance;

            if ($this->reportDuration == 'day') {

                $this->currentSupplier['initialBalance'] += PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', '<', $this->day)->sum('debt')
                    - PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', '<', $this->day)->sum('paid')
                    - PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', '<', $this->day)->sum('discount');

                $this->currentSupplier['initialSalesBalance'] += SaleDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', '<', $this->day)->sum('debt')
                    - SaleDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', '<', $this->day)->sum('paid')
                    - SaleDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', '<', $this->day)->sum('discount');

                $this->saleDebts = \App\Models\SaleDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', $this->day)->orderBy('due_date')->get();
                $this->purchaseDebts = \App\Models\PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', $this->day)->orderBy('due_date')->get();
            } elseif ($this->reportDuration == 'duration') {

                $this->currentSupplier['initialBalance'] += PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', '<', $this->from)->sum('debt')
                    - PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', '<', $this->from)->sum('paid')
                    - PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', '<', $this->from)->sum('discount');

                $this->currentSupplier['initialSalesBalance'] += SaleDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', '<', $this->from)->sum('debt')
                    - SaleDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', '<', $this->from)->sum('paid')
                    - SaleDebt::where('supplier_id', $this->currentSupplier['id'])->where('due_date', '<', $this->from)->sum('discount');

                $this->saleDebts = \App\Models\SaleDebt::where('supplier_id', $this->currentSupplier['id'])->whereBetween('due_date', [$this->from, $this->to])->orderBy('due_date')->get();
                $this->purchaseDebts = \App\Models\PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->whereBetween('due_date', [$this->from, $this->to])->orderBy('due_date')->get();
            } else {
                $this->saleDebts = \App\Models\SaleDebt::where('supplier_id', $this->currentSupplier['id'])->orderBy('due_date')->get();
                $this->purchaseDebts = \App\Models\PurchaseDebt::where('supplier_id', $this->currentSupplier['id'])->orderBy('due_date')->get();

            }

            $this->currentSalesBalance = $this->saleDebts->sum("debt") - $this->saleDebts->sum("paid");
            $this->currentPurchasesBalance = $this->purchaseDebts->sum("debt") - $this->purchaseDebts->sum("paid");

        } elseif ($this->reportType == 'employee') {   // employee
            $this->currentEmployee['initialBalance'] = \App\Models\Employee::find($this->currentEmployee['id'])->initialBalance;

            if ($this->reportDuration == 'day') {

                $this->currentEmployee['initialBalance'] += SaleDebt::where('employee_id', $this->currentEmployee['id'])->where('due_date', '<', $this->day)->sum('debt')
                    - SaleDebt::where('employee_id', $this->currentEmployee['id'])->where('due_date', '<', $this->day)->sum('paid')
                    - SaleDebt::where('employee_id', $this->currentEmployee['id'])->where('due_date', '<', $this->day)->sum('discount');

                $this->saleDebts = \App\Models\SaleDebt::where('employee_id', $this->currentEmployee['id'])->where('due_date', $this->day)->get();
                $this->employeeGifts = \App\Models\EmployeeGift::where('employee_id', $this->currentEmployee['id'])->where('gift_date', $this->day)->get();
            } elseif ($this->reportDuration == 'duration') {

                $this->currentEmployee['initialBalance'] += SaleDebt::where('employee_id', $this->currentEmployee['id'])->where('due_date', '<', $this->from)->sum('debt')
                    - SaleDebt::where('employee_id', $this->currentEmployee['id'])->where('due_date', '<', $this->from)->sum('paid')
                    - SaleDebt::where('employee_id', $this->currentEmployee['id'])->where('due_date', '<', $this->from)->sum('discount');

                $this->saleDebts = \App\Models\SaleDebt::where('employee_id', $this->currentEmployee['id'])->whereBetween('due_date', [$this->from, $this->to])->get();
                $this->employeeGifts = \App\Models\EmployeeGift::where('employee_id', $this->currentEmployee['id'])->whereBetween('gift_date', [$this->from, $this->to])->get();

            } else {
                $this->saleDebts = \App\Models\SaleDebt::where('employee_id', $this->currentEmployee['id'])->get();
                $this->employeeGifts = \App\Models\EmployeeGift::where('employee_id', $this->currentEmployee['id'])->get();
            }
            $this->salesBalance = $this->currentEmployee['initialBalance'] + $this->saleDebts->sum('debt') - $this->saleDebts->sum('paid') - $this->saleDebts->sum('discount');

            $this->currentSalesBalance = $this->salesBalance;

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
            $this->quantity = 0;
            foreach ($this->sales as $sale) {
                $this->sum += $sale->quantity * $sale->price;
                if (!empty($this->currentProduct)) {
                    $this->quantity += $sale->quantity;
                }
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
            $this->quantity = 0;

            foreach ($this->purchases as $purchase) {
                $this->sum += $purchase->quantity * $purchase->price;
                if (!empty($this->currentProduct)) {
                    $this->quantity += $purchase->quantity;
                }
            }
        } elseif ($this->reportType == "tracking") {
            $this->array = [];
            $this->sale = 0;
            $this->purchase = 0;

            $this->currentProduct['initialStock'] = \App\Models\Product::find($this->currentProduct['id'])->initialStock;

            if ($this->reportDuration == "day") {
                $this->currentProduct['initialStock'] += PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')->where('purchases.purchase_date', '<', $this->day)->where('product_id', $this->currentProduct['id'])->sum("quantity")
                    - SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')->where('sales.sale_date', '<', $this->day)->where('product_id', $this->currentProduct['id'])->sum('quantity');

                $sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')->where('sales.sale_date', $this->day)->where('product_id', $this->currentProduct['id'])->get();
                $purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')->where('purchases.purchase_date', $this->day)->where('product_id', $this->currentProduct['id'])->get();

            } elseif ($this->reportDuration == "duration") {

                $this->currentProduct['initialStock'] += PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')->where('purchases.purchase_date', '<', $this->from)->where('product_id', $this->currentProduct['id'])->sum("quantity")
                    - SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')->where('sales.sale_date', '<', $this->from)->where('product_id', $this->currentProduct['id'])->sum('quantity');


                $sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')->whereBetween('sales.sale_date', [$this->from, $this->to])->where('product_id', $this->currentProduct['id'])->get();
                $purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')->whereBetween('purchases.purchase_date', [$this->from, $this->to])->where('product_id', $this->currentProduct['id'])->get();
            } else {
                $sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')->where('product_id', $this->currentProduct['id'])->get();
                $purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')->where('product_id', $this->currentProduct['id'])->get();
            }

            $this->purchase += $this->currentProduct['initialStock'];


            foreach ($sales as $index => $sale) {

                $this->array[$sale['created_at'] . $index]['invoice'] = $sale->sale->saleDebts->first();
                $this->array[$sale['created_at'] . $index]['invoice']['sale_id'] = $sale->sale->id;

                $this->array[$sale['created_at'] . $index]['date'] = $sale['sale_date'];
                $this->array[$sale['created_at'] . $index]['note'] = $sale->client_id == null ? ($sale->supplier_id == null ? $sale->sale->employee->employeeName : $sale->sale->supplier->supplierName) : $sale->sale->client->clientName;
                $this->array[$sale['created_at'] . $index]['sale'] = $sale['quantity'];
                $this->array[$sale['created_at'] . $index]['purchase'] = 0;
                $this->sale += $sale['quantity'];
            }


            foreach ($purchases as $index => $purchase) {

                $this->array[$purchase['created_at'] . $index]['invoice'] = $purchase->purchase->purchaseDebts->first();


                $this->array[$purchase['created_at'] . $index]['date'] = $purchase['purchase_date'];
                $this->array[$purchase['created_at'] . $index]['note'] = $purchase->purchase->supplier->supplierName;
                $this->array[$purchase['created_at'] . $index]['sale'] = 0;
                $this->array[$purchase['created_at'] . $index]['purchase'] = $purchase['quantity'];
                $this->purchase += $purchase['quantity'];

            }

            ksort($this->array);


        } elseif ($this->reportType == "safe" || $this->reportType == "daily") {

            $this->paid = 0;
            $this->debt = 0;
            $this->saleFuture = 0;
            $this->purchaseFuture = 0;
            $this->bankBalance = 0;
            $this->safeBalance = 0;
            $this->array = [];

            if ($this->reportDuration == "day") {
                $this->sales = SaleDebt::where("due_date", $this->day)->get();
                $this->purchases = PurchaseDebt::where("due_date", $this->day)->get();
                $this->transfers = Transfer::where("transfer_date", $this->day)->get();
                $this->expenses = \App\Models\Expense::where("expense_date", $this->day)->get();
                $this->employeeGifts = \App\Models\EmployeeGift::where("gift_date", $this->day)->get();
                $this->withdraws = \App\Models\Withdraw::where("due_date", $this->day)->get();

            } elseif ($this->reportDuration == "duration") {
                $this->sales = SaleDebt::whereBetween("due_date", [$this->from, $this->to])->get();
                $this->purchases = PurchaseDebt::whereBetween("due_date", [$this->from, $this->to])->get();
                $this->transfers = Transfer::whereBetween("transfer_date", [$this->from, $this->to])->get();
                $this->expenses = \App\Models\Expense::whereBetween("expense_date", [$this->from, $this->to])->get();
                $this->employeeGifts = \App\Models\EmployeeGift::whereBetween("gift_date", [$this->from, $this->to])->get();
                $this->withdraws = \App\Models\Withdraw::whereBetween("due_date", [$this->from, $this->to])->get();
            } else {
                $this->sales = SaleDebt::get();
                $this->purchases = PurchaseDebt::get();
                $this->transfers = Transfer::get();
                $this->expenses = \App\Models\Expense::get();
                $this->employeeGifts = \App\Models\EmployeeGift::get();
                $this->withdraws = \App\Models\Withdraw::get();
            }

            if ($this->payment != "") {
                $this->sales = $this->sales->where("payment", $this->payment);
                $this->purchases = $this->purchases->where("payment", $this->payment);
                $this->expenses = $this->expenses->where("payment", $this->payment);
                $this->employeeGifts = $this->employeeGifts->where("payment", $this->payment);
                $this->withdraws = $this->withdraws->where("payment", $this->payment);
            }

            if (($this->reportDuration == "" || ($this->reportDuration == "duration" && Safe::first()->startingDate >= $this->from) || ($this->reportDuration == "day" && Safe::first()->startingDate == $this->day)) && ($this->payment == "" || $this->payment == "cash")) {
                $safe = Safe::first();

                if ($safe) {
                    $this->array[$safe['created_at'] . $safe['id']]['date'] = $safe['startingDate'];
                    $this->array[$safe['created_at'] . $safe['id']]['note'] = "الرصيد الافتتاحي";
                    $this->array[$safe['created_at'] . $safe['id']]['account'] = "الرصيد الافتتاحي";
                    $this->array[$safe['created_at'] . $safe['id']]['payment'] = "cash";
                    $this->array[$safe['created_at'] . $safe['id']]['name'] = "الخزنه";
                    $this->array[$safe['created_at'] . $safe['id']]['paid'] = $safe['initialBalance'];
                    $this->array[$safe['created_at'] . $safe['id']]['debt'] = 0;
                    $this->array[$safe['created_at'] . $safe['id']]['saleFuture'] = 0;
                    $this->array[$safe['created_at'] . $safe['id']]['purchaseFuture'] = 0;
                }
            }


            foreach ($this->sales as $index => $sale) {
                $this->array[$sale['created_at'] . $index]['date'] = $sale['due_date'];
                if ($sale['client_id'] != null) {
                    $note = $sale['note'];
                    $name = $sale->client->clientName;
                } elseif ($sale['supplier_id'] != null) {
                    $note = $sale['note'];
                    $name = $sale->supplier->supplierName;
                } else {
                    $note = $sale['note'];
                    $name = $sale->employee->employeeName;
                }
                $this->array[$sale['created_at'] . $index]['note'] = $note;
                $this->array[$sale['created_at'] . $index]['sale_id'] = $sale['sale_id'];
                if ($sale["sale_id"] != null) {
                    $this->array[$sale['created_at'] . $index]['invoice'] = $sale;
                }
                $this->array[$sale['created_at'] . $index]['name'] = $name;
                $this->array[$sale['created_at'] . $index]['account'] = "العملاء";
                $this->array[$sale['created_at'] . $index]['payment'] = $sale["payment"];
                if ($sale["sale_id"] != null && $sale["type"] == "debt") {
                    $this->array[$sale['created_at'] . $index]['paid'] = 0;
                    $this->array[$sale['created_at'] . $index]['saleFuture'] = $sale["debt"];
                    $this->array[$sale['created_at'] . $index]['debt'] = 0;
                } else {
                    $this->array[$sale['created_at'] . $index]['paid'] = $sale["paid"];
                    $this->array[$sale['created_at'] . $index]['debt'] = $sale["debt"];
                    $this->array[$sale['created_at'] . $index]['saleFuture'] = 0;
                }
                $this->array[$sale['created_at'] . $index]['purchaseFuture'] = 0;

            }


            foreach ($this->purchases as $index => $purchase) {
                $this->array[$purchase['created_at'] . $index]['date'] = $purchase['due_date'];
                $this->array[$purchase['created_at'] . $index]['purchase_id'] = $purchase['purchase_id'];
                if ($purchase["purchase_id"] != null) {
                    $this->array[$purchase['created_at'] . $index]['invoice'] = $purchase;
                }
                $this->array[$purchase['created_at'] . $index]['note'] = $purchase['note'];
                $this->array[$purchase['created_at'] . $index]['name'] = $purchase->supplier->supplierName;
                $this->array[$purchase['created_at'] . $index]['account'] = "الموردين";
                $this->array[$purchase['created_at'] . $index]['payment'] = $purchase["payment"];
                if ($purchase["purchase_id"] != null && $purchase["type"] == "debt") {
                    $this->array[$purchase['created_at'] . $index]['paid'] = 0;
                    $this->array[$purchase['created_at'] . $index]['purchaseFuture'] = $purchase["debt"];
                    $this->array[$purchase['created_at'] . $index]['debt'] = 0;
                } else {
                    $this->array[$purchase['created_at'] . $index]['purchaseFuture'] = 0;
                    $this->array[$purchase['created_at'] . $index]['paid'] = $purchase["debt"];
                    $this->array[$purchase['created_at'] . $index]['debt'] = $purchase["paid"];
                }
                $this->array[$purchase['created_at'] . $index]['saleFuture'] = 0;

            }

            foreach ($this->transfers as $index => $transfer) {
                if ($transfer['transfer_type'] == "cash_to_bank") {
                    $this->array[$transfer['created_at'] . $index . 1]['date'] = $transfer['transfer_date'];
                    $this->array[$transfer['created_at'] . $index . 1]['note'] = "صادر كاش";
                    $this->array[$transfer['created_at'] . $index . 1]['account'] = "تحويلات";
                    $this->array[$transfer['created_at'] . $index . 1]['payment'] = "cash";
                    $this->array[$transfer['created_at'] . $index . 1]['name'] = $transfer->note;
                    $this->array[$transfer['created_at'] . $index . 1]['paid'] = 0;
                    $this->array[$transfer['created_at'] . $index . 1]['debt'] = $transfer['transfer_amount'];
                    $this->array[$transfer['created_at'] . $index . 1]['saleFuture'] = 0;
                    $this->array[$transfer['created_at'] . $index . 1]['purchaseFuture'] = 0;

                    $this->array[$transfer['created_at'] . $index . 2]['date'] = $transfer['transfer_date'];
                    $this->array[$transfer['created_at'] . $index . 2]['note'] = "وارد بنك";
                    $this->array[$transfer['created_at'] . $index . 2]['account'] = "تحويلات";
                    $this->array[$transfer['created_at'] . $index . 2]['payment'] = "bank";
                    $this->array[$transfer['created_at'] . $index . 2]['name'] = $transfer->note;
                    $this->array[$transfer['created_at'] . $index . 2]['paid'] = $transfer['transfer_amount'];
                    $this->array[$transfer['created_at'] . $index . 2]['debt'] = 0;
                    $this->array[$transfer['created_at'] . $index . 2]['saleFuture'] = 0;
                    $this->array[$transfer['created_at'] . $index . 2]['purchaseFuture'] = 0;
                } else {
                    $this->array[$transfer['created_at'] . $index . 1]['date'] = $transfer['transfer_date'];
                    $this->array[$transfer['created_at'] . $index . 1]['note'] = "صادر بنك";
                    $this->array[$transfer['created_at'] . $index . 1]['account'] = "تحويلات";
                    $this->array[$transfer['created_at'] . $index . 1]['payment'] = "bank";
                    $this->array[$transfer['created_at'] . $index . 1]['name'] = $transfer->note;
                    $this->array[$transfer['created_at'] . $index . 1]['paid'] = 0;
                    $this->array[$transfer['created_at'] . $index . 1]['debt'] = $transfer['transfer_amount'];
                    $this->array[$transfer['created_at'] . $index . 1]['saleFuture'] = 0;
                    $this->array[$transfer['created_at'] . $index . 1]['purchaseFuture'] = 0;

                    $this->array[$transfer['created_at'] . $index . 2]['date'] = $transfer['transfer_date'];
                    $this->array[$transfer['created_at'] . $index . 2]['note'] = "وارد كاش";
                    $this->array[$transfer['created_at'] . $index . 2]['account'] = "تحويلات";
                    $this->array[$transfer['created_at'] . $index . 2]['payment'] = "cash";
                    $this->array[$transfer['created_at'] . $index . 2]['name'] = $transfer->note;
                    $this->array[$transfer['created_at'] . $index . 2]['paid'] = $transfer['transfer_amount'];
                    $this->array[$transfer['created_at'] . $index . 2]['debt'] = 0;
                    $this->array[$transfer['created_at'] . $index . 2]['saleFuture'] = 0;
                    $this->array[$transfer['created_at'] . $index . 2]['purchaseFuture'] = 0;
                }

            }

            foreach ($this->expenses as $index => $expense) {
                $this->array[$expense['created_at'] . $index]['date'] = $expense['expense_date'];
                $this->array[$expense['created_at'] . $index]['note'] = $expense['description'];
                $this->array[$expense['created_at'] . $index]['account'] = "المصروفات";
                $this->array[$expense['created_at'] . $index]['payment'] = $expense['payment'];
                $this->array[$expense['created_at'] . $index]['name'] = $expense->option_id != null ? $expense->option->optionName : "";
                $this->array[$expense['created_at'] . $index]['paid'] = 0;
                $this->array[$expense['created_at'] . $index]['debt'] = $expense['amount'];
                $this->array[$expense['created_at'] . $index]['saleFuture'] = 0;
                $this->array[$expense['created_at'] . $index]['purchaseFuture'] = 0;
            }

            foreach ($this->employeeGifts as $index => $gift) {
                $this->array[$gift['created_at'] . $index]['date'] = $gift['gift_date'];
                $this->array[$gift['created_at'] . $index]['note'] = $gift['note'] == "" ? "تم دفع مبلغ الى الموظف " : $gift['note'];
                $this->array[$gift['created_at'] . $index]['name'] = $gift->employee->employeeName;
                $this->array[$gift['created_at'] . $index]['account'] = "الموظفين";
                $this->array[$gift['created_at'] . $index]['payment'] = $gift['payment'];
                $this->array[$gift['created_at'] . $index]['paid'] = 0;
                $this->array[$gift['created_at'] . $index]['debt'] = $gift['gift_amount'];
                $this->array[$gift['created_at'] . $index]['saleFuture'] = 0;
                $this->array[$gift['created_at'] . $index]['purchaseFuture'] = 0;
            }


            foreach ($this->withdraws as $index => $withdraw) {
                $this->array[$withdraw['created_at'] . $index]['date'] = $withdraw['due_date'];
                $this->array[$withdraw['created_at'] . $index]['note'] = "تم السحب من الخزنة";
                $this->array[$withdraw['created_at'] . $index]['name'] = "اليوميه";
                $this->array[$withdraw['created_at'] . $index]['account'] = "الخزنه";
                $this->array[$withdraw['created_at'] . $index]['payment'] = $withdraw['payment'];
                $this->array[$withdraw['created_at'] . $index]['paid'] = $withdraw['amount'];
                $this->array[$withdraw['created_at'] . $index]['debt'] = 0;
                $this->array[$withdraw['created_at'] . $index]['saleFuture'] = 0;
                $this->array[$withdraw['created_at'] . $index]['purchaseFuture'] = 0;
            }

            ksort($this->array);

            foreach ($this->array as $item) {
                $this->paid += $item['paid'];
                $this->debt += $item['debt'];

                if (isset($item['sale_id'])) {
                    $this->saleFuture += $item['saleFuture'];
                }

                if (isset($item['purchase_id'])) {
                    $this->purchaseFuture += $item['purchaseFuture'];
                }

                if ($item['payment'] == "cash" && $item['saleFuture'] == 0 && $item['purchaseFuture'] == 0) {
                    $this->safeBalance += $item['paid'];
                    $this->safeBalance -= $item['debt'];
                } elseif ($item['payment'] == "bank" && $item['saleFuture'] == 0 && $item['purchaseFuture'] == 0) {
                    $this->bankBalance += $item['paid'];
                    $this->bankBalance -= $item['debt'];
                }
            }
        }
    }

    public function clearArray()
    {
        $this->array = [];
    }

    public function getInvoice($debt)
    {
        if (isset($debt['sale_id']) || isset($debt['purchase_id'])) {
            $type = isset($debt['sale_id']) ? 'sale' : 'purchase';
            $this->invoice['id'] = $debt[$type . '_id'];
            $this->invoice['type'] = $type;
            $this->invoice['date'] = $debt['due_date'];
            if (isset($debt['client_id']) && $debt['client_id'] != null) {
                $this->invoice['client'] = \App\Models\Client::find($debt['client_id'])->clientName;
                $this->invoice['clientType'] = 'العميل';
            } elseif (isset($debt['supplier_id']) && $debt['supplier_id'] != null) {
                $this->invoice['client'] = \App\Models\Supplier::find($debt['supplier_id'])->supplierName;
                $this->invoice['clientType'] = 'المورد';
            } elseif (isset($debt['employee_id']) && $debt['employee_id'] != null) {
                $this->invoice['client'] = \App\Models\Employee::find($debt['employee_id'])->employeeName;
                $this->invoice['clientType'] = 'الموظف';
            }
            if ($type == 'sale') {
                $this->invoice['cart'] = SaleDetail::where('sale_id', $this->invoice['id'])->join('products', 'products.id', '=', 'sale_details.product_id')->get()->toArray();
                $row = \App\Models\Sale::where('id', $this->invoice['id'])->first();
            } else {
                $this->invoice['cart'] = PurchaseDetail::where('purchase_id', $this->invoice['id'])->join('products', 'products.id', '=', 'purchase_details.product_id')->get()->toArray();
                $row = \App\Models\Purchase::where('id', $this->invoice['id'])->first();
            }
            $this->invoice['paid'] = $row['paid'];
            $this->invoice['remainder'] = $row['remainder'];
            $this->invoice['total_amount'] = $row['total_amount'];
            $this->invoice['discount'] = floatval($row['discount']);
            $this->invoice['amount'] = $row['total_amount'] + floatval($row['discount']);
            $this->invoice['showMode'] = false;
            $this->dispatch('sale_created', $this->invoice);
        }
    }

    public function resetData()
    {
        $this->reset('sum', 'salesSum', 'currentClient', 'currentSupplier', 'debtsSum', 'paysSum', 'purchasesSum', 'expensesSum', 'employeesSum', 'damagedsSum', 'percent');
    }

    public function dbBackup()
    {
        Artisan::call("backup:run  --only-db");
        $this->alert('success', 'تم النسخ الإحتياطي بنجاح', ['timerProgressBar' => true]);
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
        } elseif ($this->reportType == 'employee') {
            $this->employees = \App\Models\Employee::where('employeeName', 'LIKE', '%' . $this->employeeSearch . '%')->get();
        } elseif ($this->reportType == 'sales' || $this->reportType == 'purchases' || $this->reportType == 'tracking') {
            if ($this->store_id == 0) {
                $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->get();
            } else {
                $this->products = \App\Models\Product::where('productName', 'LIKE', '%' . $this->productSearch . '%')->where('store_id', $this->store_id)->get();
            }
        }
        return view('livewire.report');
    }
}
