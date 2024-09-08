<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Employee extends Model
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

    public function debts()
    {
        return $this->hasMany(SaleDebt::class);
    }

    public function saleReturns()
    {
        return $this->hasManyThrough(SaleReturn::class, Sale::class);
    }

    public function getCurrentBalanceAttribute()
    {
        $creditReturnsTotal = $this->saleReturns()->sum(DB::raw('quantity * price')) - $this->saleReturns->sum('amount');

        return $this->initialBalance + $this->sales()->sum("remainder") + $this->debts()->where("type", "debt")->sum("amount") + $this->debts()->sum("service") - $this->debts()->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getPastBalance($date)
    {
        $creditReturnsTotal = $this->saleReturns()->where("sale_returns.due_date", "<", $date)->sum(DB::raw('quantity * price')) - $this->saleReturns->where("sale_returns.due_date", "<", $date)->sum('amount');

        return $this->initialBalance + $this->sales()->where("due_date", "<", $date)->sum("remainder") + $this->debts()->where("type", "debt")->where("due_date", "<", $date)->sum("amount") + $this->debts()->where("due_date", "<", $date)->sum("service") - $this->debts()->where("due_date", "<", $date)->where("type", "pay")->sum("amount") - $creditReturnsTotal;
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

    public function getDayBalance($date)
    {
        $initial = $this->startingDate == $date ? $this->initialBalance : 0;
        $creditReturnsTotal = $this->saleReturns()->where("sale_returns.due_date", $date)->sum(DB::raw('quantity * price')) - $this->saleReturns->where("sale_returns.due_date", $date)->sum('amount');

        return $initial + $this->sales()->where("due_date", "<", $date)->sum("remainder") + $this->debts()->where("due_date", $date)->where("type", "debt")->sum("amount") + $this->debts()->where("due_date", $date)->sum("service") - $this->debts()->where("due_date", $date)->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getBetweenBalance($from, $to)
    {
        $initial = ($this->startingDate >= $from && $this->startingDate <= $to) ? $this->initialBalance : 0;
        $creditReturnsTotal = $this->saleReturns()->whereBetween("due_date", [$from, $to])->sum(DB::raw('quantity * price')) - $this->saleReturns->whereBetween("due_date", [$from, $to])->sum('amount');

        return $initial + $this->sales()->whereBetween("due_date", [$from, $to])->sum("remainder") + $this->debts()->whereBetween("due_date", [$from, $to])->where("type", "debt")->sum("amount") + $this->debts()->whereBetween("due_date", [$from, $to])->sum("service") - $this->debts()->whereBetween("due_date", [$from, $to])->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

}

