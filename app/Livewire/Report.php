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
    public string $clientSearch = '';
    public string $supplierSearch = '';
    public string $employeeSearch = '';
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
    public float $salesPaidSum = 0;
    public float $purchasesPaidSum = 0;
    /**
     * @var float|mixed
     */
    public float $currentSalesBalance = 0;
    public float $currentPurchasesBalance = 0;
    public Collection $merged;
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

            $this->stock = floatval(\App\Models\Product::selectRaw('SUM(stock * purchase_price) as totalStockValue')->first()->totalStockValue);

            $this->capital = Safe::first()->capital ?? 0;
            $safe = \App\Models\Safe::count() != 0 ? \App\Models\Safe::first()->initialBalance : 0;

            $this->bankBalance = Bank::sum('initialBalance')
                + SaleDebt::where("type", "pay")->where("payment", "bank")->sum("paid")
                - SaleDebt::where("type", "debt")->whereNull("sale_id")->where("payment", "bank")->sum("debt")
                - Transfer::where("transfer_type", "bank_to_cash")->sum("transfer_amount")
                + Transfer::where("transfer_type", "cash_to_bank")->sum("transfer_amount")
                - Expense::where("payment", "bank")->sum("amount")
                - EmployeeGift::where("payment", "bank")->sum("gift_amount")
                - PurchaseDebt::where("type", "pay")->where("payment", "bank")->sum("paid")
                + PurchaseDebt::where("type", "debt")->whereNull("purchase_id")->where("payment", "bank")->sum("debt");

            $this->balance = $safe
                + SaleDebt::where("type", "pay")->where("payment", "cash")->sum("paid")
                - SaleDebt::where("type", "debt")->whereNull("sale_id")->where("payment", "cash")->sum("debt")
                - Transfer::where("transfer_type", "cash_to_bank")->sum("transfer_amount")
                + Transfer::where("transfer_type", "bank_to_cash")->sum("transfer_amount")
                - Expense::where("payment", "cash")->sum("amount")
                - EmployeeGift::where("payment", "bank")->sum("gift_amount")
                - PurchaseDebt::where("type", "pay")->where("payment", "cash")->sum("paid")
                + PurchaseDebt::where("type", "debt")->whereNull("purchase_id")->where("payment", "cash")->sum("debt");

            $this->totalExpenses = Expense::sum("amount") + EmployeeGift::sum("gift_amount");
            $this->clients = \App\Models\Client::get();

            $sum = 0;
            $this->deposits = 0;
            foreach ($this->clients as $client) {
                $sum = $client->initialBalance + $client->debts->sum("debt") - $client->debts->sum("paid");
                if ($sum < 0) {
                    $this->deposits += -1 * $sum;
                }
            }

            $this->suppliers = \App\Models\Supplier::get();

            $sum = 0;
            $this->creditors = 0;
            foreach ($this->suppliers as $supplier) {
                $sum += $supplier->initialBalance + $supplier->purchaseDebts->sum("debt") - $supplier->purchaseDebts->sum("paid");
                $sum -= $supplier->initialSalesBalance + $supplier->saleDebts->sum("debt") - $supplier->saleDebts->sum("paid");
                if ($sum > 0) {
                    $this->creditors += $sum;
                }

            }

            $this->assets = $this->stock + $this->bankBalance + $this->balance + $this->totalSales;
            $this->adversaries = $this->capital + $this->deposits + $this->creditors;

            $this->salesDebts = $this->salesSum - $this->salesPaidSum;

            $this->purchasesDebts = $this->purchasesSum - $this->purchasesPaidSum;


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
            $this->salesBalance = $this->currentClient['initialBalance'] + $this->saleDebts->sum('debt') - $this->saleDebts->sum('paid') - $this->saleDebts->sum('discount');
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

            $this->salesBalance = $this->currentSupplier['initialSalesBalance'] + $this->saleDebts->sum('debt') - $this->saleDebts->sum('paid') - $this->saleDebts->sum('discount');

            $this->purchasesBalance = $this->currentSupplier['initialBalance'] + $this->purchaseDebts->sum('debt') - $this->purchaseDebts->sum('paid') - $this->purchaseDebts->sum('discount');

            $this->currentSalesBalance = $this->salesBalance;

            $this->currentPurchasesBalance = $this->purchasesBalance;

            $this->merged = $this->saleDebts->merge($this->purchaseDebts);
            $this->merged->sortBy('created_at');
        } elseif ($this->reportType == 'employee') {   // employee
            if ($this->reportDuration == 'day') {
                $this->saleDebts = \App\Models\SaleDebt::where('employee_id', $this->currentEmployee['id'])->where('due_date', $this->day)->get();
                $this->employeeGifts = \App\Models\EmployeeGift::where('employee_id', $this->currentEmployee['id'])->where('gift_date', $this->day)->get();
            } elseif ($this->reportDuration == 'duration') {
                $this->saleDebts = \App\Models\SaleDebt::where('employee_id', $this->currentEmployee['id'])->whereBetween('due_date', [$this->from, $this->to])->get();
                $this->employeeGifts = \App\Models\EmployeeGift::where('employee_id', $this->currentEmployee['id'])->whereBetween('gift_date', [$this->from, $this->to])->get();
            } else {
                $this->saleDebts = \App\Models\SaleDebt::where('employee_id', $this->currentEmployee['id'])->get();
                $this->employeeGifts = \App\Models\EmployeeGift::where('employee_id', $this->currentEmployee['id'])->get();
            }

            $this->salesBalance = $this->currentEmployee['initialBalance'] + $this->saleDebts->sum('debt') - $this->saleDebts->sum('paid') - $this->saleDebts->sum('discount');

            $this->currentSalesBalance = $this->salesBalance;

            $this->merged = $this->saleDebts->merge($this->employeeGifts);
            $this->merged->sortBy('created_at');
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
        } elseif ($this->reportType == "safe") {

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

            $this->array = [];

            foreach ($this->sales as $sale) {
                $this->array[$sale['created_at']]['date'] = $sale['due_date'];
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
                $this->array[$sale['created_at']]['note'] = $note;
                $this->array[$sale['created_at']]['sale_id'] = $sale['sale_id'];
                if ($sale["sale_id"] != null) {
                    $this->array[$sale['created_at']]['invoice'] = $sale;
                }
                $this->array[$sale['created_at']]['name'] = $name;
                $this->array[$sale['created_at']]['account'] = "العملاء";
                $this->array[$sale['created_at']]['paid'] = $sale["paid"];
                $this->array[$sale['created_at']]['debt'] = $sale["debt"];
            }

            foreach ($this->purchases as $purchase) {
                $this->array[$purchase['created_at']]['date'] = $purchase['due_date'];
                $this->array[$purchase['created_at']]['purchase_id'] = $purchase['purchase_id'];
                if ($purchase["purchase_id"] != null) {
                    $this->array[$purchase['created_at']]['invoice'] = $purchase;
                }
                $this->array[$purchase['created_at']]['note'] = $purchase['note'];
                $this->array[$purchase['created_at']]['name'] = $purchase->supplier->supplierName;
                $this->array[$purchase['created_at']]['account'] = "الموردين";
                $this->array[$purchase['created_at']]['paid'] = $purchase["debt"];
                $this->array[$purchase['created_at']]['debt'] = $purchase["paid"];

            }

            foreach ($this->transfers as $transfer) {
                $this->array[$transfer['created_at']]['date'] = $transfer['transfer_date'];
                $this->array[$transfer['created_at']]['note'] = $transfer['transfer_type'] == "cash_to_bank" ? "تحويل من كاش الى بنك " . $transfer['note'] : "تحويل من بنك الى كاش " . $transfer['note'];
                $this->array[$transfer['created_at']]['account'] = "تحويلات";
                $this->array[$transfer['created_at']]['name'] = $transfer->note;
                $this->array[$transfer['created_at']]['paid'] = $transfer['transfer_amount'];
                $this->array[$transfer['created_at']]['debt'] = $transfer['transfer_amount'];
            }

            foreach ($this->expenses as $expense) {
                $this->array[$expense['created_at']]['date'] = $expense['expense_date'];
                $this->array[$expense['created_at']]['note'] = $expense['description'];
                $this->array[$expense['created_at']]['account'] = "المصروفات";
                $this->array[$expense['created_at']]['name'] = "";
                $this->array[$expense['created_at']]['paid'] = 0;
                $this->array[$expense['created_at']]['debt'] = $expense['amount'];
            }

            foreach ($this->employeeGifts as $gift) {
                $this->array[$gift['created_at']]['date'] = $gift['gift_date'];
                $this->array[$gift['created_at']]['note'] = $gift['note'] == "" ? "تم دفع مبلغ الى الموظف " : $gift['note'];
                $this->array[$gift['created_at']]['name'] = $gift->employee->employeeName;
                $this->array[$gift['created_at']]['account'] = "الموظفين";
                $this->array[$gift['created_at']]['paid'] = 0;
                $this->array[$gift['created_at']]['debt'] = $gift['gift_amount'];
            }

            foreach ($this->withdraws as $withdraw) {
                $this->array[$withdraw['created_at']]['date'] = $withdraw['due_date'];
                $this->array[$withdraw['created_at']]['note'] = "تم السحب من الخزنة";
                $this->array[$withdraw['created_at']]['name'] = "";
                $this->array[$withdraw['created_at']]['account'] = "الخزنه";
                $this->array[$withdraw['created_at']]['paid'] = $withdraw['amount'];
                $this->array[$withdraw['created_at']]['debt'] = 0;
            }

            ksort($this->array);

            $this->safeBalance = Safe::first()->initialBalance
                + $this->withdraws->sum("amount")
                + $this->sales->where("type", "pay")->where("payment", "cash")->sum("paid")
                - $this->sales->where("type", "debt")->where("payment", "cash")->whereNull("sale_id")->sum("debt")
                + $this->transfers->where("transfer_type", "bank_to_cash")->sum("transfer_amount")
                - $this->transfers->where("transfer_type", "cash_to_bank")->sum("transfer_amount")
                - $this->expenses->where("payment", "cash")->sum("amount")
                - $this->employeeGifts->where("payment", "cash")->sum("gift_amount")
                - $this->purchases->where("type", "pay")->where("payment", "cash")->sum("paid")
                + $this->purchases->where("type", "debt")->where("payment", "cash")->whereNull("purchase_id")->sum("debt");

            $this->bankBalance = Bank::sum('initialBalance')
                + $this->sales->where("type", "pay")->where("payment", "bank")->sum("paid")
                - $this->sales->where("type", "debt")->where("payment", "cash")->whereNull("sale_id")->sum("debt")
                + $this->transfers->where("transfer_type", "cash_to_bank")->sum("transfer_amount")
                - $this->transfers->where("transfer_type", "bank_to_cash")->sum("transfer_amount")
                - $this->expenses->where("payment", "bank")->sum("amount")
                - $this->employeeGifts->where("payment", "bank")->sum("gift_amount")
                - $this->purchases->where("type", "pay")->where("payment", "bank")->sum("paid")
                + $this->purchases->where("type", "debt")->where("payment", "bank")->whereNull("purchase_id")->sum("debt");

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
