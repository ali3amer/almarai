<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\ClientDebt;
use App\Models\EmployeeDebt;
use App\Models\EmployeeGift;
use App\Models\Expense;
use App\Models\People;
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
use function Symfony\Component\Translation\t;

class Report extends Component
{
    use LivewireAlert;

    public string $title = 'التقارير';
    public bool $show = false;


    public string $reportType = '';
    public int $store_id = 0;
    public float $sum = 0;
    public string $day = '';
    public string $from = '';
    public string $to = '';
    public string $reportDuration = '';

    public array $currentPeople = [];
    public array $currentProduct = [];
    public array $cart = [];

    public array $reportTypes = [
        0 => '-------------------------',
        'general' => 'تقرير عام',
        'inventory' => 'تقرير جرد',
        'client' => 'تقرير عميل',
        'employee' => 'تقرير موظف',
        'supplier' => 'تقرير مورد',
        'deposit' => 'تقرير العهد والأمانات',
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
        'deposit_debts' => 'العهد والامانات',
        'expenses' => 'المصروفات',
        'withdraws' => 'الخزنه',
        'transfers' => 'التحويلات',
    ];

    public $clientType = [
        'client' => 'عميل',
        'supplier' => 'مورد',
        'employee' => 'موظف',
        'deposit' => 'عهد',
    ];
    public string $search = '';

    public array $purchases = [];
    public array $sales = [];
    public collection $stores;
    public array $saleDebts = [];
    public array $purchaseDebts = [];
    public array $depositDebts = [];
    public collection $clients;
    public collection $people;
    public collection $debts;
    public collection $products;
    public array $trackingProducts = [];
    public string $payment = '';
    public string $clientSearch = '';
    public string $supplierSearch = '';
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
    public float $salesBalance = 0;
    public float $depositsBalance = 0;
    public float $total = 0;
    public Collection $transfers;
    public Collection $expenses;
    public array $employeeGifts = [];
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
    public $purchasesBalance = 0;
    public $totalClientsBalance = 0;
    public $totalSuppliersBalance = 0;
    public $totalSafeBalance = 0;
    public $totalBanksBalance = 0;
    public $totalProductsStock = 0;
    public $totalDepositsBalance = 0;
    public array $statements = [];
    public $giftsBalance = 0;
    public $initialSafeBalance = 0;
    public $initialBankBalance = 0;

    public function choosePeople(People $people)
    {
        $this->currentPeople = $people->toArray();
        $this->currentPeople['initialGiftsBalance'] = 0;
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
                $this->totalBanksBalance = (new \App\Models\Bank)->getPastBankBalance($this->day) + (new \App\Models\Bank)->getDayBankBalance($this->day);

                $this->totalSafeBalance = (new \App\Models\Safe)->getPastSafeBalance($this->day) +  (new \App\Models\Safe)->getSafeDayBalance($this->day);

                $this->totalClientsBalance = \App\Models\People::all()->sum(function ($client) {
                    return $client->getPastSalesBalance($this->day) + $client->getDaySalesBalance($this->day);
                });

                $this->totalExpenses = EmployeeGift::where("due_date", $this->day)->sum("amount") + Expense::where("due_date", $this->day)->sum("amount");

                $this->totalSuppliersBalance = \App\Models\People::all()->sum(function ($supplier) {
                    return $supplier->getPastPurchasesBalance($this->day) + $supplier->getDayPurchasesBalance($this->day);
                });

                $this->totalDepositsBalance = \App\Models\People::all()->sum(function ($deposit) {
                    return $deposit->getPastDepositsBalance($this->day) + $deposit->getDayDepositsBalance($this->day);
                });

                $this->people = \App\Models\People::all()->map(function ($people) {
                    $people->purchasesBalance = $people->getPastPurchasesBalance($this->day) + $people->getDayPurchasesBalance($this->day);
                    $people->salesBalance = $people->getPastSalesBalance($this->day) + $people->getDaySalesBalance($this->day);
                    $people->depositsBalance = $people->getPastDepositsBalance($this->day) + $people->getDayDepositsBalance($this->day);
                    return $people;
                });

            } elseif ($this->reportDuration == 'duration') {

                $this->totalProductsStock = \App\Models\Product::all()->sum(function ($product) {
                    return $product->stock * $product->getPrice($this->to);
                });
                $this->totalBanksBalance = (new \App\Models\Safe())->getPastSafeBalance($this->from) + (new \App\Models\Safe)->getSafeBetweenBalance($this->from, $this->to);
                $this->totalSafeBalance = (new \App\Models\Bank)->getPastBankBalance($this->from) + (new \App\Models\Bank)->getBetweenBalance($this->from, $this->to);

                $this->totalClientsBalance = \App\Models\People::all()->sum(function ($client) {
                    return $client->getPastSalesBalance($this->from) + $client->getsalesBetweenBalance($this->from, $this->to);
                });

                $this->totalExpenses = EmployeeGift::whereBetween("due_date", [$this->from, $this->to])->sum("amount") + Expense::whereBetween("due_date", [$this->from, $this->to])->sum("amount");

                $this->totalSuppliersBalance = \App\Models\People::all()->sum(function ($supplier) {
                    return $supplier->getPastPurchasesBalance($this->from) + $supplier->getPurchasesBetweenBalance($this->from, $this->to);
                });
                $this->totalDepositsBalance = \App\Models\People::all()->sum(function ($deposit) {
                    return $deposit->getPastDepositsBalance($this->from) + $deposit->getDepositsBetweenBalance($this->from, $this->to);
                });


                $this->people = \App\Models\People::all()->map(function ($people) {
                    $people->purchasesBalance = $people->getPastPurchasesBalance($this->from) + $people->getPurchasesBetweenBalance($this->from, $this->to);
                    $people->salesBalance = $people->getPastSalesBalance($this->from) + $people->getSalesBetweenBalance($this->from, $this->to);
                    $people->depositsBalance = $people->getPastDepositsBalance($this->from) + $people->getDepositsBetweenBalance($this->from, $this->to);
                    return $people;
                });

            } else {
                $this->totalProductsStock = \App\Models\Product::all()->sum(function ($product) {
                    return $product->stock * $product->purchase_price;
                });
                $this->totalBanksBalance = (new \App\Models\Bank)->getCurrentTotalBalance();
                $this->totalSafeBalance = Safe::first()->currentBalance;

                $this->totalClientsBalance = \App\Models\People::all()->sum(function ($client) {
                    return $client->currentSalesBalance;
                });

                $this->totalExpenses = EmployeeGift::sum("amount") + Expense::sum("amount");

                $this->totalSuppliersBalance = \App\Models\People::all()->sum(function ($supplier) {
                    return $supplier->currentPurchasesBalance;
                });
                $this->totalDepositsBalance = \App\Models\People::all()->sum(function ($deposit) {
                    return $deposit->currentDepositsBalance;
                });

                $this->people = \App\Models\People::all()->map(function ($people) {
                    $people->purchasesBalance = $people->currentPurchasesBalance;
                    $people->salesBalance = $people->currentSalesBalance;
                    $people->depositsBalance = $people->currentDepositsBalance;
                    return $people;
                });

            }

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
        } elseif ($this->reportType == 'client' || $this->reportType == 'supplier' || $this->reportType == 'employee' || $this->reportType == 'deposit') {   // supplier
            $people = \App\Models\People::find($this->currentPeople['id']);
            if ($this->reportDuration == 'day') {
                $this->currentPeople['initialPurchasesBalance'] = $people->getPastPurchasesBalance($this->day);
                $this->currentPeople['initialSalesBalance'] = $people->getPastSalesBalance($this->day);
                $this->currentPeople['initialDepositsBalance'] = $people->getPastDepositsBalance($this->day);
                $this->currentPeople['initialGiftsBalance'] = $people->getPastGiftsBalance($this->day);

                $this->depositDebts = $people->depositDebts->where('due_date', $this->day)->toArray();
                $this->employeeGifts = (new \App\Models\Employee)->getMovements(id: $this->currentPeople['id'], duration: "day", from: $this->day);
                $this->saleDebts = (new \App\Models\Sale)->getMovements(id: $this->currentPeople['id'], duration: "day", from: $this->day);
                $this->purchaseDebts = (new \App\Models\Purchase)->getMovements(id: $this->currentPeople['id'], duration: "day", from: $this->day);
                $this->salesBalance = $this->currentPeople['initialSalesBalance'] + $people->getDaySalesBalance($this->day);
                $this->purchasesBalance = $this->currentPeople['initialPurchasesBalance'] + $people->getDayPurchasesBalance($this->day);
                $this->depositsBalance = $this->currentPeople['initialDepositsBalance'] + $people->getDayDepositsBalance($this->day);
                $this->giftsBalance = $this->currentPeople['initialGiftsBalance'] + $people->getDayGiftsBalance($this->day);
            } elseif ($this->reportDuration == 'duration') {
                $this->currentPeople['initialPurchasesBalance'] = $people->getPastPurchasesBalance($this->from);
                $this->currentPeople['initialSalesBalance'] = $people->getPastSalesBalance($this->from);
                $this->currentPeople['initialDepositsBalance'] = $people->getPastDepositsBalance($this->from);
                $this->currentPeople['initialGiftsBalance'] = $people->getPastGiftsBalance($this->from);

                $this->depositDebts = $people->depositDebts->whereBetween('due_date', [$this->from, $this->to])->toArray();
                $this->employeeGifts = (new \App\Models\Employee)->getMovements(id: $this->currentPeople['id'], duration: "duration", from: $this->from, to: $this->to);
                $this->saleDebts = (new \App\Models\Sale)->getMovements(id: $this->currentPeople['id'], duration: "duration", from: $this->from, to: $this->to);
                $this->purchaseDebts = (new \App\Models\Purchase)->getMovements(id: $this->currentPeople['id'], duration: "duration", from: $this->from, to: $this->to);
                $this->salesBalance = $this->currentPeople['initialSalesBalance'] + $people->getSalesBetweenBalance($this->from, $this->to);
                $this->purchasesBalance = $this->currentPeople['initialPurchasesBalance'] + $people->getPurchasesBetweenBalance($this->from, $this->to);
                $this->depositsBalance = $this->currentPeople['initialDepositsBalance'] + $people->getDepositsBetweenBalance($this->from, $this->to);
                $this->giftsBalance = $this->currentPeople['initialGiftsBalance'] + $people->getGiftsBetweenBalance($this->from, $this->to);
            } else {
                $this->currentPeople['initialPurchasesBalance'] = $people->initialPurchasesBalance;
                $this->currentPeople['initialSalesBalance'] = $people->initialSalesBalance;
                $this->currentPeople['initialDepositsBalance'] = $people->initialDepositsBalance;
                $this->currentPeople['initialGiftsBalance'] = 0;

                $this->depositDebts = $people->depositDebts->toArray();
                $this->employeeGifts = (new \App\Models\Employee)->getMovements($this->currentPeople['id']);
                $this->saleDebts = (new \App\Models\Sale)->getMovements($this->currentPeople['id']);
                $this->purchaseDebts = (new \App\Models\Purchase)->getMovements($this->currentPeople['id']);
                $this->depositsBalance = $people->currentDepositsBalance;
                $this->salesBalance = $people->currentSalesBalance;
                $this->purchasesBalance = $people->currentPurchasesBalance;
                $this->giftsBalance = $people->currentGiftsBalance;
            }

        } elseif ($this->reportType == 'sales') {  // sale

            $sales = SaleDetail::join('sales', 'sales.id', '=', 'sale_details.sale_id')
                ->join('products', 'products.id', '=', 'sale_details.product_id')
                ->leftJoin('people', 'people.id', '=', 'sales.people_id')
                ->select(
                    'sale_details.*',
                    'sales.due_date',
                    'products.productName',
                    DB::raw("people.name as ownerName")
                )->get();


            if ($this->reportDuration == 'day') {
                if (!empty($this->currentProduct)) {
                    $sales = $this->sales->where('product_id', $this->currentProduct['id'])->where('sales.due_date', $this->day);
                } else {
                    $sales = $sales->where('due_date', $this->day);
                }
            } elseif ($this->reportDuration == 'duration') {
                if (!empty($this->currentProduct)) {
                    $sales = $sales->whereBetween('due_date', [$this->from, $this->to])->where('product_id', $this->currentProduct['id']);
                } else {
                    $sales = $sales->whereBetween('due_date', [$this->from, $this->to]);
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
                ->leftJoin('people', 'people.id', '=', 'purchases.people_id')
                ->select(
                    'purchase_details.*',
                    'purchases.due_date',
                    'products.productName',
                    'people.name as ownerName',
                )->get();

            if ($this->reportDuration == 'day') {
                if (!empty($this->currentProduct)) {
                    $purchases = $purchases->where('product_id', $this->currentProduct['id'])->where('$purchases.due_date', $this->day);
                } else {
                    $purchases = $purchases->where('due_date', $this->day);
                }
            } elseif ($this->reportDuration == 'duration') {
                if (!empty($this->currentProduct)) {
                    $purchases = $purchases->whereBetween('due_date', [$this->from, $this->to])->where('product_id', $this->currentProduct['id']);
                } else {
                    $purchases = $purchases->whereBetween('due_date', [$this->from, $this->to]);
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
                $optionName = $expenses->first()->option->optionName ?? "غير مصنف";
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
                $sales = (new \App\Models\Sale)->getMovements(duration: "day", from: $this->day);
                $purchases = (new \App\Models\Purchase)->getMovements(duration: "day", from: $this->day);
                $deposits = (new \App\Models\Deposit)->getMovements(duration: "day", from: $this->day);
                $expenses = (new \App\Models\Expense)->getMovements(duration: "day", from: $this->day);
                $gifts = (new \App\Models\Employee)->getMovements(duration: "day", from: $this->day);
                $withdraws = (new \App\Models\Withdraw)->getMovements(duration: "day", from: $this->day);
                $transfers = (new \App\Models\Transfer)->getMovements(duration: "day", from: $this->day);
                $this->initialSafeBalance = (new \App\Models\Safe)->getPastSafeBalance($this->day);
                $this->safeBalance = (new \App\Models\Safe)->getPastSafeBalance($this->day) + (new \App\Models\Safe)->getSafeDayBalance($this->day);
                $this->initialBankBalance = (new \App\Models\Bank)->getPastBankBalance($this->day);
                $this->bankBalance = (new \App\Models\Bank)->getPastBankBalance($this->day) + (new \App\Models\Bank)->getDayBankBalance($this->day);
            } elseif ($this->reportDuration == "duration") {
                $sales = (new \App\Models\Sale)->getMovements(duration: "duration", from: $this->from, to: $this->to);
                $purchases = (new \App\Models\Purchase)->getMovements(duration: "duration", from: $this->from, to: $this->to);
                $deposits = (new \App\Models\Deposit)->getMovements(duration: "duration", from: $this->from, to: $this->to);
                $expenses = (new \App\Models\Expense)->getMovements(duration: "duration", from: $this->from, to: $this->to);
                $gifts = (new \App\Models\Employee)->getMovements(duration: "duration", from: $this->from, to: $this->to);
                $withdraws = (new \App\Models\Withdraw)->getMovements(duration: "duration", from: $this->from, to: $this->to);
                $transfers = (new \App\Models\Transfer)->getMovements(duration: "duration", from: $this->from, to: $this->to);
                $this->initialSafeBalance = (new \App\Models\Safe)->getPastSafeBalance($this->from);
                $this->safeBalance = $this->initialSafeBalance + (new \App\Models\Safe)->getSafeBetweenBalance($this->from, $this->to);
                $this->initialBankBalance = (new \App\Models\Bank)->getPastBankBalance($this->from);
                $this->bankBalance = $this->initialBankBalance + (new \App\Models\Bank)->getBetweenBalance($this->from, $this->to);

            } else {
                $sales = (new \App\Models\Sale)->getMovements();
                $purchases = (new \App\Models\Purchase)->getMovements();
                $deposits = (new \App\Models\Deposit)->getMovements();
                $expenses = (new \App\Models\Expense)->getMovements();
                $gifts = (new \App\Models\Employee)->getMovements();
                $withdraws = (new \App\Models\Withdraw)->getMovements();
                $transfers = (new \App\Models\Transfer)->getMovements();
                $this->initialSafeBalance = Safe::first()->initialBalance;
                $this->safeBalance = Safe::first()->currentBalance;
                $this->initialBankBalance = Bank::sum("initialBalance");
                $this->bankBalance = (new \App\Models\Bank)->getCurrentTotalBalance();
            }

            $allMovements = array_merge($sales, $purchases, $deposits, $expenses, $gifts, $withdraws, $transfers);

            if ($this->payment == "cash") {
                $this->statements = collect($allMovements)->where("payment", "cash")->sortBy(['due_date', 'created_at', 'invoice_id'])->toArray();
            } elseif ($this->payment == "bank") {
                $this->statements = collect($allMovements)->where("payment", "bank")->sortBy(['due_date', 'created_at', 'invoice_id'])->toArray();
            } else {
                $this->statements = collect($allMovements)->sortBy(['due_date', 'created_at', 'invoice_id'])->toArray();
            }
        }
    }

    public function clearArray()
    {
        $this->statements = [];
        $this->resetData();
    }

    public function getInvoice($id, $tableName)
    {
        $type = ($tableName == "sales" || $tableName == "sale_returns") ? 'sale' : 'purchase';
        $this->invoice['id'] = $id;
        $this->invoice['type'] = $type;
        if ($type == "sale") {
            $invoice = \App\Models\Sale::find($id);

            $this->invoice['client'] = $invoice->people->name;
            $this->invoice['clientType'] = $this->clientType[$invoice->people->type];

            $this->invoice['cart'] = SaleDetail::where('sale_id', $this->invoice['id'])->join('products', 'products.id', '=', 'sale_details.product_id')->get()->toArray();
            $row = \App\Models\Sale::where('id', $this->invoice['id'])->first();
        } else {
            $invoice = \App\Models\Purchase::find($id);
            $this->invoice['client'] = $invoice->people->name;
            $this->invoice['clientType'] = $this->clientType[$invoice->people->type];

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
        $this->reset('sum', 'currentPeople', 'percent', 'reportDuration', 'day', 'from', 'to', 'currentProduct', 'store_id');
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
        if ($this->reportType == 'client' || $this->reportType == 'supplier' || $this->reportType == 'employee' || $this->reportType == 'deposit') {
            $this->clients = \App\Models\People::where("type", $this->reportType)->where('name', 'LIKE', '%' . $this->clientSearch . '%')->get();
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
