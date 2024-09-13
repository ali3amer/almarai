<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class People extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function gifts()
    {
        return $this->hasMany(EmployeeGift::class);
    }

    public function saleDebts()
    {
        return $this->hasMany(SaleDebt::class);
    }

    public function saleReturns()
    {
        return $this->hasManyThrough(SaleReturn::class, Sale::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchaseDebts()
    {
        return $this->hasMany(PurchaseDebt::class);
    }

    public function purchaseReturns()
    {
        return $this->hasManyThrough(PurchaseReturn::class, Purchase::class);
    }

    public function depositDebts()
    {
        return $this->hasMany(DepositDebt::class);
    }

    public function getMovements()
    {
        $gifts = EmployeeGift::select(
            DB::raw("'employees' as tableName"),
            DB::raw("null as clientType"),
            DB::raw('null as type'),
            DB::raw('null as invoice_id'),
            'employee_gifts.id',
            DB::raw('0 as income'),
            DB::raw('amount as expense'),
            DB::raw('0 as futureIncome'),
            DB::raw("0 as futureExpense"),
            'due_date',
            'payment',
            'bank',
            'bank_id',
            'note',
            DB::raw('null as owner_id'),
            DB::raw("employees.employeeName as ownerName"),
            'employee_gifts.created_at',
            'employee_gifts.updated_at'
        )
            ->join('employees', 'employees.id', '=', 'employee_gifts.employee_id')->orderBy('due_date', 'asc')->get();


        return $gifts;
    }

    public function getCurrentSalesBalanceAttribute()
    {
        $creditReturnsTotal = $this->saleReturns()->sum(DB::raw('quantity * price')) - $this->saleReturns->sum('amount');

        return $this->initialSalesBalance + $this->sales()->sum("remainder") - $this->saleDebts()->where("type", "discount")->sum("amount") + $this->saleDebts()->where("type", "debt")->sum("amount") - $this->saleDebts()->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getPastSalesBalance($date)
    {
        $creditReturnsTotal = $this->saleReturns()->where("sale_returns.due_date", "<", $date)->sum(DB::raw('quantity * price')) - $this->saleReturns->where("sale_returns.due_date", "<", $date)->sum('sale_returns.amount');

        return $this->initialSalesBalance + $this->sales()->where("due_date", "<", $date)->sum("remainder") - $this->saleDebts()->where("due_date", "<", $date)->where("type", "discount")->sum("amount") + $this->saleDebts()->where("due_date", "<", $date)->where("type", "debt")->sum("amount") - $this->saleDebts()->where("due_date", "<", $date)->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getDaySalesBalance($date)
    {
        $initial = $this->startingDate == $date ? $this->initialSalesBalance : 0;
        $creditReturnsTotal = $this->saleReturns()->where("sale_returns.due_date", $date)->sum(DB::raw('quantity * price')) - $this->saleReturns->where("sale_returns.due_date", $date)->sum('sale_returns.amount');

        return $initial + $this->sales()->where("due_date", "<", $date)->sum("remainder") - $this->saleDebts()->where("due_date", $date)->where("type", "discount")->sum("amount") + $this->saleDebts()->where("due_date", $date)->where("type", "debt")->sum("amount") - $this->saleDebts()->where("due_date", $date)->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getSalesBetweenBalance($from, $to)
    {
        $initial = ($this->startingDate >= $from && $this->startingDate <= $to) ? $this->initialSalesBalance : 0;
        $creditReturnsTotal = $this->saleReturns()->whereBetween("sale_returns.due_date", [$from, $to])->sum(DB::raw('quantity * price')) - $this->saleReturns->whereBetween("sale_returns.due_date", [$from, $to])->sum('sale_returns.amount');

        return $initial + $this->sales()->whereBetween("due_date", [$from, $to])->sum("remainder") - $this->saleDebts()->whereBetween("due_date", [$from, $to])->where("type", "discount")->sum("amount") + $this->saleDebts()->whereBetween("due_date", [$from, $to])->where("type", "debt")->sum("amount") - $this->saleDebts()->whereBetween("due_date", [$from, $to])->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getCurrentPurchasesBalanceAttribute()
    {
        $creditReturnsTotal = $this->purchaseReturns()->sum(DB::raw('quantity * price')) - $this->purchaseReturns->sum('amount');

        return $this->initialPurchasesBalance + $this->purchases()->sum("remainder") - $this->purchaseDebts()->where("type", "discount")->sum("amount") + $this->purchaseDebts()->where("type", "debt")->sum("amount") - $this->purchaseDebts()->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getPastPurchasesBalance($date)
    {
        $creditReturnsTotal = $this->purchaseReturns()->where("purchase_returns.due_date", "<", $date)->sum(DB::raw('quantity * price')) - $this->purchaseReturns->where("purchase_returns.due_date", "<", $date)->sum('purchase_returns.amount');

        return $this->initialPurchasesBalance + $this->purchases()->where("due_date", "<", $date)->sum("remainder") - $this->purchaseDebts()->where("due_date", "<", $date)->where("type", "discount")->sum("amount") + $this->purchaseDebts()->where("type", "debt")->where("due_date", "<", $date)->sum("amount") - $this->purchaseDebts()->where("due_date", "<", $date)->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getDayPurchasesBalance($date)
    {
        $initial = $this->startingDate == $date ? $this->initialPurchasesBalance : 0;
        $creditReturnsTotal = $this->purchaseReturns()->where("purchase_returns.due_date", $date)->sum(DB::raw('quantity * price')) - $this->purchaseReturns->where("purchase_returns.due_date", $date)->sum('purchase_returns.amount');

        return $initial + $this->purchases()->where("due_date", "<", $date)->sum("remainder") - $this->purchaseDebts()->where("due_date", $date)->where("type", "discount")->sum("amount") + $this->purchaseDebts()->where("due_date", $date)->where("type", "debt")->sum("amount") - $this->purchaseDebts()->where("due_date", $date)->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getPurchasesBetweenBalance($from, $to)
    {
        $initial = ($this->startingDate >= $from && $this->startingDate <= $to) ? $this->initialPurchasesBalance : 0;
        $creditReturnsTotal = $this->purchaseReturns()->whereBetween("purchase_returns.due_date", [$from, $to])->sum(DB::raw('quantity * price')) - $this->purchaseReturns->whereBetween("purchase_returns.due_date", [$from, $to])->sum('purchase_returns.amount');

        return $initial + $this->purchases()->whereBetween("due_date", [$from, $to])->sum("remainder") - $this->purchaseDebts()->whereBetween("due_date", [$from, $to])->where("type", "discount")->sum("amount") + $this->purchaseDebts()->whereBetween("due_date", [$from, $to])->where("type", "debt")->sum("amount") - $this->purchaseDebts()->whereBetween("due_date", [$from, $to])->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getCurrentDepositsBalanceAttribute()
    {
        return $this->initialDepositsBalance + $this->depositDebts()->where("type", "pay")->sum("amount") - $this->depositDebts()->where("type", "debt")->sum("amount");
    }

    public function getPastDepositsBalance($date)
    {
        return $this->initialDepositsBalance + $this->depositDebts()->where("type", "pay")->where("due_date", "<", $date)->sum("amount") - $this->depositDebts()->where("type", "debt")->where("due_date", "<", $date)->sum("amount");
    }

    public function getDayDepositsBalance($date)
    {
        $initial = $this->startingDate == $date ? $this->initialDepositsBalance : 0;
        return $initial + $this->depositDebts()->where("type", "pay")->where("due_date", $date)->sum("amount") - $this->depositDebts()->where("type", "debt")->where("due_date", $date)->sum("amount");
    }

    public function getDepositsBetweenBalance($from, $to)
    {
        $initial = ($this->startingDate >= $from && $this->startingDate <= $to) ? $this->initialDepositsBalance : 0;
        return $initial + $this->depositDebts()->where("type", "pay")->whereBetween("due_date", [$from, $to])->sum("amount") - $this->depositDebts()->where("type", "debt")->whereBetween("due_date", [$from, $to])->sum("amount");
    }
}
