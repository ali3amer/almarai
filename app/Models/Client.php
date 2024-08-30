<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sales()
    {
        return $this->hasMany(Sale::class);
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

        return $this->initialBalance + $this->sales()->sum("remainder") + $this->debts()->where("type", "debt")->sum("amount") - $this->debts()->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getPastBalance($date)
    {
        $creditReturnsTotal = $this->saleReturns()->where("sale_returns.due_date", "<", $date)->sum(DB::raw('quantity * price')) - $this->saleReturns->where("sale_returns.due_date", "<", $date)->sum('amount');

        return $this->initialBalance + $this->sales()->where("due_date", "<", $date)->sum("remainder") + $this->debts()->where("due_date", "<", $date)->where("type", "debt")->sum("amount") + $this->debts()->where("due_date", "<", $date)->sum("service") - $this->debts()->where("due_date", "<", $date)->where("type", "pay")->sum("amount") - $creditReturnsTotal;
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
