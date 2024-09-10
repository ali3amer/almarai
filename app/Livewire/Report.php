<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\ClientDebt;
use App\Models\EmployeeDebt;
use App\Models\EmployeeGift;
use App\Models\Expense;
use App\Models\PurchaseDebt;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use App\Models\Safe;
use App\Models\SaleDebt;
use App\Models\SaleDetail;
use App\Models\SaleReturn;
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
        'expenses' => 'تقرير المصروفات',
        'tracking' => 'تقرير متابعة حركة صنف',
        'daily' => 'تقرير القيود اليومية',
        'safe' => 'تقرير خزنة',
    ];
    public array $reportDurations = [
        0 => '-------------------------',
        'day' => 'تقرير يوميه',
        'duration' => 'تقرير فتره',
    ];

    public array $accounts = [
        'sales' => 'المبيعات',
        'sale_returns' => 'مرتجعات المبيعات',
        'sale_debts' => 'العملاء',
        'purchases' => 'المشتريات',
        'purchase_returns' => 'مرتجعات المشتريات',
        'purchase_debts' => 'الموردون',
        'employees' => 'الموظفين',
        'deposits' => 'العهد والامانات',
        'expenses' => 'المصروفات',
        'withdraws' => 'الخزنه',
        'transfers' => 'التحويلات',
    ];

    public string $search = '';

    public array $purchases = [];
    public array $sales = [];
    public collection $stores;
    public array $saleDebts = [];
    public array $purchaseDebts = [];
    public collection $clients;
    public collection $debts;
    public collection $pays;
    public collection $suppliers;
    public collection $employees;
    public collection $products;
    public array $trackingProducts = [];
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
    public $percent = 0;
    public string $productSearch = '';
    public array $invoice = [];
    public Collection $deposits;
    public float $salesBalance = 0;
    public float $total = 0;
    public array $merged = [];
    public Collection $transfers;
    public Collection $expenses;
    public Collection $employeeGifts;
    public $balance = 0;
    public $quantity = 0;
    public $stock;
    public $bankBalance;
    public Collection $withdraws;
    public $capital = 0;
    public $totalExpenses = 0;
    public $assets = 0;
    public $adversaries = 0;
    public \Illuminate\Support\Collection $expensesByOptions;
    public $purcasesBalance = 0;
    public $totalClientsBalance = 0;
    public $totalSuppliersBalance = 0;
    public $totalSafeBalance = 0;
    public $totalBanksBalance = 0;
    public $totalProductsStock = 0;
    public $totalDepositsBalance = 0;
    public array $statements = [];

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

                $this->totalProductsStock = \App\Models\Product::all()->sum(function ($product) {
                    return $product->stock * $product->getPrice($this->day);
                });
                $this->totalBanksBalance = (new \App\Models\Bank)->getCurrentTotalBalance();
                $this->totalSafeBalance = Safe::first()->currentBalance;
                $clientsSales = \App\Models\Client::all()->sum(function ($client) {
                    return $client->getDayBalance($this->day);
                });

                $suppliersSales = \App\Models\Supplier::all()->sum(function ($supplier) {
                    return $supplier->getSalesDayBalance($this->day);
                });

                $employeesSales = \App\Models\Employee::all()->sum(function ($employee) {
                    return $employee->getDayBalance($this->day);
                });

                $this->totalExpenses = EmployeeGift::where("due_date", $this->day)->sum("amount") + Expense::where("due_date", $this->day)->sum("amount");

                $this->totalSuppliersBalance = \App\Models\Supplier::all()->sum(function ($supplier) {
                    return $supplier->getDayBalance($this->day);
                });
                $this->totalDepositsBalance = \App\Models\Deposit::all()->sum(function ($deposit) {
                    return $deposit->getDayBalance($this->day);
                });

                $this->clients = \App\Models\Client::all()->map(function ($client) {
                    $client->salesBalance = $client->getDayBalance($this->day);
                    return $client;
                });

                $this->suppliers = \App\Models\Supplier::all()->map(function ($supplier) {
                    $supplier->purchasesBalance = $supplier->getDayBalance($this->day);
                    $supplier->salesBalance = $supplier->getSalesDayBalance($this->day);
                    return $supplier;
                });

                $this->employees = \App\Models\Employee::all()->map(function ($employee) {
                    $employee->salesBalance = $employee->getDayBalance($this->day);
                    return $employee;
                });

                $this->deposits = \App\Models\Deposit::all()->map(function ($deposit) {
                    $deposit->salesBalance = $deposit->getDayBalance($this->day);
                    return $deposit;
                });

            } elseif ($this->reportDuration == 'duration') {

                $this->totalProductsStock = \App\Models\Product::all()->sum(function ($product) {
                    return $product->stock * $product->getPrice($this->to);
                });
                $this->totalBanksBalance = (new \App\Models\Bank)->getCurrentTotalBalance();
                $this->totalSafeBalance = Safe::first()->currentBalance;

                $clientsSales = \App\Models\Client::all()->sum(function ($client) {
                    return $client->getBetweenBalance($this->from, $this->to);
                });

                $suppliersSales = \App\Models\Supplier::all()->sum(function ($supplier) {
                    return $supplier->getsalesBetweenBalance($this->from, $this->to);
                });

                $employeesSales = \App\Models\Employee::all()->sum(function ($employee) {
                    return $employee->getBetweenBalance($this->from, $this->to);
                });
                $this->totalExpenses = EmployeeGift::whereBetween("due_date", [$this->from, $this->to])->sum("amount") + Expense::whereBetween("due_date", [$this->from, $this->to])->sum("amount");

                $this->totalSuppliersBalance = \App\Models\Supplier::all()->sum(function ($supplier) {
                    return $supplier->getBetweenBalance($this->from, $this->to);
                });
                $this->totalDepositsBalance = \App\Models\Deposit::all()->sum(function ($deposit) {
                    return $deposit->getBetweenBalance($this->from, $this->to);
                });

                $this->clients = \App\Models\Client::all()->map(function ($client) {
                    $client->salesBalance = $client->getBetweenBalance($this->from, $this->to);
                    return $client;
                });

                $this->suppliers = \App\Models\Supplier::all()->map(function ($supplier) {
                    $supplier->purchasesBalance = $supplier->getBetweenBalance($this->from, $this->to);
                    $supplier->salesBalance = $supplier->getSalesBetweenBalance($this->from, $this->to);
                    return $supplier;
                });

                $this->employees = \App\Models\Employee::all()->map(function ($employee) {
                    $employee->salesBalance = $employee->getBetweenBalance($this->from, $this->to);
                    return $employee;
                });

                $this->deposits = \App\Models\Deposit::all()->map(function ($deposit) {
                    $deposit->salesBalance = $deposit->getBetweenBalance($this->from, $this->to);
                    return $deposit;
                });

            } else {
                $this->totalProductsStock = \App\Models\Product::all()->sum(function ($product) {
                    return $product->stock * $product->purchase_price;
                });
                $this->totalBanksBalance = (new \App\Models\Bank)->getCurrentTotalBalance();
                $this->totalSafeBalance = Safe::first()->currentBalance;

                $clientsSales = \App\Models\Client::all()->sum(function ($client) {
                    return $client->currentBalance;
                });

                $suppliersSales = \App\Models\Supplier::all()->sum(function ($supplier) {
                    return $supplier->currentSalesBalance;
                });

                $employeesSales = \App\Models\Employee::all()->sum(function ($employee) {
                    return $employee->currentBalance;
                });

                $this->totalExpenses = EmployeeGift::sum("amount") + Expense::sum("amount");

                $this->totalSuppliersBalance = \App\Models\Supplier::all()->sum(function ($supplier) {
                    return $supplier->currentBalance;
                });
                $this->totalDepositsBalance = \App\Models\Deposit::all()->sum(function ($deposit) {
                    return $deposit->currentBalance;
                });

                $this->clients = \App\Models\Client::all()->map(function ($client) {
                    $client->salesBalance = $client->currentBalance;
                    return $client;
                });

                $this->suppliers = \App\Models\Supplier::all()->map(function ($supplier) {
                    $supplier->purchasesBalance = $supplier->currentBalance;
                    $supplier->salesBalance = $supplier->currentSalesBalance;
                    return $supplier;
                });

                $this->employees = \App\Models\Employee::all()->map(function ($employee) {
                    $employee->salesBalance = $employee->currentBalance;
                    return $employee;
                });

                $this->deposits = \App\Models\Deposit::all()->map(function ($deposit) {
                    $deposit->salesBalance = $deposit->currentBalance;
                    return $deposit;
                });

            }

            $this->totalClientsBalance = $clientsSales + $suppliersSales + $employeesSales;
            $this->capital = Safe::first()->capital;

            $this->assets = $this->totalProductsStock + $this->totalBanksBalance + $this->totalSafeBalance + $this->totalClientsBalance + $this->totalExpenses;
            $this->adversaries = $this->totalSuppliersBalance + $this->totalDepositsBalance + $this->capital;
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
            $client = \App\Models\Client::find($this->currentClient['id']);
            if ($this->reportDuration == 'day') {
                $this->currentClient['initialBalance'] = $client->getPastBalance($this->day);

                $saleDebts = (new \App\Models\Sale)->getMovements($this->currentClient['id'], 'client')->where('due_date', $this->day);

            } elseif ($this->reportDuration == 'duration') {
                $this->currentClient['initialBalance'] = $client->getPastBalance($this->from);

                $saleDebts = (new \App\Models\Sale)->getMovements($this->currentClient['id'], 'client')->whereBetween("due_date", "<", [$this->from, $this->to]);

            } else {
                $this->currentClient['initialBalance'] = $client->initialBalance;

                $saleDebts = (new \App\Models\Sale)->getMovements($this->currentClient['id'], 'client');
            }

            $this->saleDebts = $saleDebts->toArray();
            $this->salesBalance = $this->currentClient['initialBalance'] + $saleDebts->sum("expense") + $saleDebts->sum("futureIncome") - $saleDebts->sum("income") - $saleDebts->sum("futureExpense");
        } elseif ($this->reportType == 'supplier') {   // supplier
            $supplier = \App\Models\Supplier::find($this->currentSupplier['id']);
            if ($this->reportDuration == 'day') {
                $this->currentSupplier['initialBalance'] = $supplier->getPastBalance($this->day);
                $this->currentSupplier['initialSalesBalance'] = $supplier->getSalesPastBalance($this->day);

                $saleDebts = (new \App\Models\Sale)->getMovements($this->currentSupplier['id'], 'supplier')->where('due_date', $this->day);
                $purchaseDebts = (new \App\Models\Purchase)->getMovements($this->currentSupplier['id'], 'supplier')->where('due_date', $this->day);

            } elseif ($this->reportDuration == 'duration') {
                $this->currentSupplier['initialBalance'] = $supplier->getPastBalance($this->from);
                $this->currentSupplier['initialSalesBalance'] = $supplier->getSalesPastBalance($this->from);

                $saleDebts = (new \App\Models\Sale)->getMovements($this->currentSupplier['id'], 'supplier')->whereBetween("due_date", "<", [$this->from, $this->to]);
                $purchaseDebts = (new \App\Models\Purchase)->getMovements($this->currentSupplier['id'], 'supplier')->whereBetween("due_date", "<", [$this->from, $this->to]);

            } else {
                $this->currentSupplier['initialBalance'] = $supplier->initialBalance;
                $this->currentSupplier['initialSalesBalance'] = $supplier->initialSalesBalance;

                $saleDebts = (new \App\Models\Sale)->getMovements($this->currentSupplier['id'], 'supplier');
                $purchaseDebts = (new \App\Models\Purchase)->getMovements($this->currentSupplier['id'], 'supplier');
            }

            $this->saleDebts = $saleDebts->toArray();
            $this->purchaseDebts = $purchaseDebts->toArray();
            $this->salesBalance = $this->currentSupplier['initialSalesBalance'] + $saleDebts->sum("expense") + $saleDebts->sum("futureIncome") - $saleDebts->sum("income") - $saleDebts->sum("futureExpense");
            $this->purcasesBalance = $this->currentSupplier['initialBalance'] + $purchaseDebts->sum("expense") + $purchaseDebts->sum("futureIncome") - $purchaseDebts->sum("income") - $purchaseDebts->sum("futureExpense");

        } elseif ($this->reportType == 'employee') {   // employee
            $employee = \App\Models\Employee::find($this->currentEmployee['id']);

            if ($this->reportDuration == 'day') {

                $this->currentEmployee['initialBalance'] += $employee->getPastBalance($this->day);

                $saleDebts = (new \App\Models\Sale)->getMovements($this->currentEmployee['id'], 'employee');
                $this->employeeGifts = \App\Models\EmployeeGift::where('employee_id', $this->currentEmployee['id'])->where('due_date', $this->day)->get();
            } elseif ($this->reportDuration == 'duration') {

                $this->currentEmployee['initialBalance'] += $employee->getPastBalance($this->from);

                $saleDebts = (new \App\Models\Sale)->getMovements($this->currentEmployee['id'], 'employee');
                $this->employeeGifts = \App\Models\EmployeeGift::where('employee_id', $this->currentEmployee['id'])->whereBetween('due_date', [$this->from, $this->to])->get();

            } else {
                $this->currentEmployee['initialBalance'] += $employee->initialBalance;

                $saleDebts = (new \App\Models\Sale)->getMovements($this->currentEmployee['id'], 'employee');
                $this->employeeGifts = \App\Models\EmployeeGift::where('employee_id', $this->currentEmployee['id'])->get();
            }
            $this->salesBalance = $this->currentEmployee['initialBalance'] + $saleDebts->sum("expense") + $saleDebts->sum("futureIncome") - $saleDebts->sum("income") - $saleDebts->sum("futureExpense");

            $this->saleDebts = $saleDebts->toArray();
        } elseif ($this->reportType == 'sales') {  // sale

            $sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')
                ->join('products', 'products.id', '=', 'sale_details.product_id')
                ->leftJoin('clients', 'clients.id', '=', 'sales.client_id')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'sales.supplier_id')
                ->leftJoin('employees', 'employees.id', '=', 'sales.employee_id')
                ->select(
                    'sale_details.*',
                    'sales.due_date',
                    'products.productName',
                    DB::raw("COALESCE(clients.clientName, suppliers.supplierName, employees.employeeName) as ownerName")
                )->get();


            if ($this->reportDuration == 'day') {
                if (!empty($this->currentProduct)) {
                    $sales = $this->sales->where('product_id', $this->currentProduct['id'])->where('sales.due_date', $this->day);
                } else {
                    $sales = $sales->where('sales.due_date', $this->day);
                }
            } elseif ($this->reportDuration == 'duration') {
                if (!empty($this->currentProduct)) {
                    $sales = $sales->whereBetween('sales.due_date', [$this->from, $this->to])->where('product_id', $this->currentProduct['id']);
                } else {
                    $sales = $sales->whereBetween('sales.due_date', [$this->from, $this->to]);
                }
            } else {
                if (!empty($this->currentProduct)) {
                    $sales = $sales->where('product_id', $this->currentProduct['id']);
                }
            }

            $this->sales = $sales->toArray();

            $this->sum = 0;
            $this->quantity = 0;
            foreach ($this->sales as $sale) {
                $this->sum += $sale['quantity'] * $sale['price'];
                if (!empty($this->currentProduct)) {
                    $this->quantity += $sale['quantity'];
                }
            }

        } elseif ($this->reportType == 'purchases') {  // purchase
            $purchases = PurchaseDetail::join('purchases', 'purchases.id', '=', 'purchase_details.purchase_id')
                ->join('products', 'products.id', '=', 'purchase_details.product_id')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
                ->select(
                    'purchase_details.*',
                    'purchases.due_date',
                    'products.productName',
                    'suppliers.supplierName as ownerName',
                )->get();


            if ($this->reportDuration == 'day') {
                if (!empty($this->currentProduct)) {
                    $purchases = $purchases->where('product_id', $this->currentProduct['id'])->where('$purchases.due_date', $this->day);
                } else {
                    $purchases = $purchases->where('purchases.due_date', $this->day);
                }
            } elseif ($this->reportDuration == 'duration') {
                if (!empty($this->currentProduct)) {
                    $purchases = $purchases->whereBetween('purchases.due_date', [$this->from, $this->to])->where('product_id', $this->currentProduct['id']);
                } else {
                    $purchases = $purchases->whereBetween('purchases.due_date', [$this->from, $this->to]);
                }
            } else {
                if (!empty($this->currentProduct)) {
                    $purchases = $purchases->where('product_id', $this->currentProduct['id']);
                }
            }

            $this->purchases = $purchases->toArray();

            $this->sum = 0;
            $this->quantity = 0;
            foreach ($this->purchases as $purchase) {
                $this->sum += $purchase['quantity'] * $purchase['price'];
                if (!empty($this->currentProduct)) {
                    $this->quantity += $purchase['quantity'];
                }
            }
        } elseif ($this->reportType == "tracking") {

            if ($this->reportDuration == "day") {
                $this->currentProduct['initialStock'] = \App\Models\Product::find($this->currentProduct['id'])->getStockBeforeDate($this->day);
                $this->trackingProducts = \App\Models\Product::find($this->currentProduct['id'])->getProductMovements()->where("due_date", $this->day)->toArray();

            } elseif ($this->reportDuration == "duration") {
                $this->currentProduct['initialStock'] = \App\Models\Product::find($this->currentProduct['id'])->getStockBeforeDate($this->from);
                $this->trackingProducts = \App\Models\Product::find($this->currentProduct['id'])->getProductMovements()->whereBetween("due_date", [$this->from, $this->to])->toArray();

            } else {
                $this->currentProduct['initialStock'] = \App\Models\Product::find($this->currentProduct['id'])->initialStock;
                $this->trackingProducts = \App\Models\Product::find($this->currentProduct['id'])->getProductMovements()->toArray();
            }

        } elseif ($this->reportType == "expenses") {
            if ($this->reportDuration == "day") {
                $this->expenses = \App\Models\Expense::where("due_date", $this->day)->get();
            } elseif ($this->reportDuration == "duration") {
                $this->expenses = \App\Models\Expense::whereBetween("due_date", [$this->from, $this->to])->get();
            } else {
                $this->expenses = \App\Models\Expense::get();
            }

            $this->expensesByOptions = $this->expenses->groupBy('option_id')->map(function ($expenses) {
                $optionName = $expenses->first()->option->optionName;
                $totalAmount = $expenses->sum('amount');
                return ['option_name' => $optionName, 'total_amount' => $totalAmount];
            });

        } elseif ($this->reportType == "safe" || $this->reportType == "daily") {
            $this->paid = 0;
            $this->debt = 0;
            $this->saleFuture = 0;
            $this->purchaseFuture = 0;
            $this->bankBalance = 0;
            $this->safeBalance = 0;

            if ($this->reportDuration == "day") {
                $sales = (new \App\Models\Sale)->getMovements()->where("due_date", $this->day)->toArray();
                $purchases = (new \App\Models\Purchase)->getMovements()->where("due_date", $this->day)->toArray();
                $deposits = (new \App\Models\Deposit)->getMovements()->where("due_date", $this->day)->toArray();
                $expenses = (new \App\Models\Expense)->getMovements()->where("due_date", $this->day)->toArray();
                $gifts = (new \App\Models\Employee)->getMovements()->where("due_date", $this->day)->toArray();
                $withdraws = (new \App\Models\Withdraw)->getMovements()->where("due_date", $this->day)->toArray();
                $transfers = (new \App\Models\Transfer)->getMovements()->where("due_date", $this->day)->toArray();

                $this->transfers = Transfer::where("due_date", $this->day)->get();

                $this->safeBalance = (new \App\Models\Safe)->getSafeDayBalance($this->day);
                $this->bankBalance = (new \App\Models\Bank)->getDayBalance($this->day);
            } elseif ($this->reportDuration == "duration") {
                $sales = (new \App\Models\Sale)->getMovements()->whereBetween("due_date", [$this->from, $this->to])->toArray();
                $purchases = (new \App\Models\Purchase)->getMovements()->whereBetween("due_date", [$this->from, $this->to])->toArray();
                $deposits = (new \App\Models\Deposit)->getMovements()->whereBetween("due_date", [$this->from, $this->to])->toArray();
                $expenses = (new \App\Models\Expense)->getMovements()->whereBetween("due_date", [$this->from, $this->to])->toArray();
                $gifts = (new \App\Models\Employee)->getMovements()->whereBetween("due_date", [$this->from, $this->to])->toArray();
                $withdraws = (new \App\Models\Withdraw)->getMovements()->whereBetween("due_date", [$this->from, $this->to])->toArray();
                $transfers = (new \App\Models\Transfer)->getMovements()->whereBetween("due_date", [$this->from, $this->to])->toArray();
                $this->safeBalance = (new \App\Models\Safe)->getSafeBetweenBalance($this->from, $this->to);
                $this->bankBalance = (new \App\Models\Bank)->getBetweenBalance($this->from, $this->to);

                $this->transfers = Transfer::whereBetween("due_date", [$this->from, $this->to])->get();
            } else {
                $sales = (new \App\Models\Sale)->getMovements()->toArray();
                $purchases = (new \App\Models\Purchase)->getMovements()->toArray();
                $deposits = (new \App\Models\Deposit)->getMovements()->toArray();
                $expenses = (new \App\Models\Expense)->getMovements()->toArray();
                $gifts = (new \App\Models\Employee)->getMovements()->toArray();
                $withdraws = (new \App\Models\Withdraw)->getMovements()->toArray();
                $transfers = (new \App\Models\Transfer)->getMovements()->toArray();
                $this->safeBalance = Safe::first()->currentBalance;
                $this->bankBalance = (new \App\Models\Bank)->getCurrentTotalBalance();
            }

            if ($this->payment != '') {
                $sales = (new \App\Models\Sale)->getMovements()->where("payment", $this->payment)->toArray();
                $purchases = (new \App\Models\Purchase)->getMovements()->where("payment", $this->payment)->toArray();
                $deposits = (new \App\Models\Deposit)->getMovements()->where("payment", $this->payment)->toArray();
                $expenses = (new \App\Models\Expense)->getMovements()->where("payment", $this->payment)->toArray();
                $gifts = (new \App\Models\Employee)->getMovements()->where("payment", $this->payment)->toArray();
                $withdraws = (new \App\Models\Withdraw)->getMovements()->where("payment", $this->payment)->toArray();
                $transfers = (new \App\Models\Transfer)->getMovements()->where("payment", $this->payment)->toArray();
            }

            $allMovements = collect($sales)
                ->merge($purchases)
                ->merge($deposits)
                ->merge($expenses)
                ->merge($gifts)
                ->merge($withdraws)->merge($transfers);

            $this->statements = $allMovements->sortBy(['due_date', 'created_at', 'invoice_id'])->toArray();

        }
    }

    public function clearArray()
    {
        $this->statements = [];
    }

    public function getInvoice($id, $tableName)
    {
        $type = ($tableName == "sales" || $tableName == "sale_returns") ? 'sale' : 'purchase';
        $this->invoice['id'] = $id;
        $this->invoice['type'] = $type;
        if ($type == "sale") {
            $invoice = \App\Models\Sale::find($id);
            if ($invoice['client_id'] != null) {
                $this->invoice['client'] = $invoice->client->clientName;
                $this->invoice['clientType'] = 'العميل';
            } elseif ($invoice['supplier_id'] != null) {
                $this->invoice['client'] = $invoice->supplier->supplierName;
                $this->invoice['clientType'] = 'المورد';
            } else {
                $this->invoice['client'] = $invoice->employee->employeeName;
                $this->invoice['clientType'] = 'الموظف';
            }
            $this->invoice['cart'] = SaleDetail::where('sale_id', $this->invoice['id'])->join('products', 'products.id', '=', 'sale_details.product_id')->get()->toArray();
            $row = \App\Models\Sale::where('id', $this->invoice['id'])->first();
        } else {
            $invoice = \App\Models\Purchase::find($id);
            $this->invoice['cart'] = PurchaseDetail::where('purchase_id', $this->invoice['id'])->join('products', 'products.id', '=', 'purchase_details.product_id')->get()->toArray();
            $row = \App\Models\Purchase::where('id', $this->invoice['id'])->first();
        }
        $this->invoice['date'] = $invoice['due_date'];
        $this->invoice['paid'] = $row['paid'];
        $this->invoice['remainder'] = $row['remainder'];
        $this->invoice['amount'] = $row['amount'];
        $this->invoice['discount'] = floatval($row['discount']);
        $this->invoice['cost'] = $row['amount'] + floatval($row['discount']);
        $this->invoice['showMode'] = false;
        $this->dispatch('sale_created', $this->invoice);
    }

    public function resetData()
    {
        $this->reset('sum', 'currentClient', 'currentSupplier', 'currentEmployee', 'percent');
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
