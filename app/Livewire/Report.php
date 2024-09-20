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

    public function choosePeople($poeple)
    {
        $this->currentPeople = [];
        $this->currentPeople = $poeple;
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

                $this->totalClientsBalance = \App\Models\People::all()->sum(function ($client) {
                    return $client->getDaySalesBalance($this->day);
                });

                $this->totalExpenses = EmployeeGift::where("due_date", $this->day)->sum("amount") + Expense::where("due_date", $this->day)->sum("amount");

                $this->totalSuppliersBalance = \App\Models\People::all()->sum(function ($supplier) {
                    return $supplier->getDayPurchasesBalance($this->day);
                });
                $this->totalDepositsBalance = \App\Models\People::all()->sum(function ($deposit) {
                    return $deposit->getDayDepositsBalance($this->day);
                });

                $this->people = \App\Models\People::all()->map(function ($people) {
                    $people->purchasesBalance = $people->getDayPurchasesBalance($this->day);
                    $people->salesBalance = $people->getSalesDayBalance($this->day);
                    $people->depositsBalance = $people->getDayDepositsBalance($this->day);
                    return $people;
                });

            } elseif ($this->reportDuration == 'duration') {

                $this->totalProductsStock = \App\Models\Product::all()->sum(function ($product) {
                    return $product->stock * $product->getPrice($this->to);
                });
                $this->totalBanksBalance = (new \App\Models\Bank)->getCurrentTotalBalance();
                $this->totalSafeBalance = Safe::first()->currentBalance;

                $this->totalClientsBalance = \App\Models\People::all()->sum(function ($client) {
                    return $client->getsalesBetweenBalance($this->from, $this->to);
                });

                $this->totalExpenses = EmployeeGift::whereBetween("due_date", [$this->from, $this->to])->sum("amount") + Expense::whereBetween("due_date", [$this->from, $this->to])->sum("amount");

                $this->totalSuppliersBalance = \App\Models\People::all()->sum(function ($supplier) {
                    return $supplier->getPurchasesBetweenBalance($this->from, $this->to);
                });
                $this->totalDepositsBalance = \App\Models\People::all()->sum(function ($deposit) {
                    return $deposit->getDepositsBetweenBalance($this->from, $this->to);
                });


                $this->people = \App\Models\People::all()->map(function ($people) {
                    $people->purchasesBalance = $people->getPurchasesBetweenBalance($this->from, $this->to);
                    $people->salesBalance = $people->getSalesBetweenBalance($this->from, $this->to);
                    $people->depositsBalance = $people->getDepositsBetweenBalance($this->from, $this->to);
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
        } elseif ($this->reportType == 'client' || $this->reportType == 'supplier' || $this->reportType == 'employee') {   // supplier
            $people = \App\Models\People::find($this->currentPeople['id']);
            if ($this->reportDuration == 'day') {
                $this->currentPeople['initialPurchasesBalance'] = $people->getPastPurchasesBalance($this->day);
                $this->currentPeople['initialSalesBalance'] = $people->getPastSalesBalance($this->day);

                $saleDebts = (new \App\Models\Sale)->getMovements($this->currentPeople['id'], $this->reportType)->where('due_date', $this->day);
                $purchaseDebts = (new \App\Models\Purchase)->getMovements($this->currentPeople['id'], $this->reportType)->where('due_date', $this->day);
                $gifts = \App\Models\EmployeeGift::where('people_id', $this->currentPeople['id'])->where('due_date', $this->day)->get();

            } elseif ($this->reportDuration == 'duration') {
                $this->currentPeople['initialPurchasesBalance'] = $people->getPastPurchasesBalance($this->from);
                $this->currentPeople['initialSalesBalance'] = $people->getPastSalesBalance($this->from);

                $saleDebts = (new \App\Models\Sale)->getMovements($this->currentPeople['id'], $this->reportType)->whereBetween("due_date", [$this->from, $this->to]);
                $purchaseDebts = (new \App\Models\Purchase)->getMovements($this->currentPeople['id'], $this->reportType)->whereBetween("due_date", [$this->from, $this->to]);
                $gifts = \App\Models\EmployeeGift::where('people_id', $this->currentPeople['id'])->whereBetween('due_date', [$this->from, $this->to])->get();

            } else {
                $this->currentPeople['initialPurchasesBalance'] = $people->initialPurchasesBalance;
                $this->currentPeople['initialSalesBalance'] = $people->initialSalesBalance;

                $saleDebts = (new \App\Models\Sale)->getMovements($this->currentPeople['id'], $this->reportType);
                $purchaseDebts = (new \App\Models\Purchase)->getMovements($this->currentPeople['id'], $this->reportType);
                $gifts = \App\Models\EmployeeGift::where('people_id', $this->currentPeople['id'])->get();

            }

            $this->employeeGifts = $gifts->toArray();
            $this->saleDebts = $saleDebts->toArray();
            $this->purchaseDebts = $purchaseDebts->toArray();
            $this->salesBalance = $this->currentPeople['initialSalesBalance'] + $saleDebts->sum("expense") + $saleDebts->sum("futureIncome") - $saleDebts->sum("income") - $saleDebts->sum("futureExpense");
            $this->purchasesBalance = $this->currentPeople['initialPurchasesBalance'] + $purchaseDebts->sum("expense") + $purchaseDebts->sum("futureIncome") - $purchaseDebts->sum("income") - $purchaseDebts->sum("futureExpense");

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
        if ($this->reportType == 'client' || $this->reportType == 'supplier' || $this->reportType == 'employee') {
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
