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


    public function getMovements($id = null, $duration = null, $from = null, $to = null)
    {
        $array = [];

        if ($duration == "day") {
            $gifts = EmployeeGift::where("due_date", $from)->get();

        } elseif ($duration == "duration") {
            $gifts = EmployeeGift::whereBetween("due_date", [$from, $to])->get();
        } else {
            $gifts = EmployeeGift::all();
        }


        if ($id != null) {
            $gifts = $gifts->where("people_id", $id);
        }
        foreach ($gifts as $gift) {
            $array[] = [
                "tableName" => "employees",
                "clientType" => $gift->people->type,
                "type" => $gift->type,
                "real" => !($gift->type == "discount"),
                "invoice_id" => null,
                "id" => $gift->id,
                "debit" => ($gift->type == "salary" || $gift->type == "debt") ? $gift->amount : 0,
                "credit" => ($gift->type == "discount" || $gift->type == "pay") ? $gift->amount : 0,
                "due_date" => $gift->due_date,
                "payment" => $gift->payment,
                "bank" => $gift->bank,
                "bank_id" => $gift->bank_id,
                "note" => $gift->note,
                "owner_id" => $gift->people_id,
                "ownerName" => $gift->people->name,
                "created_at" => $gift->created_at,
                "updated_at" => $gift->updated_at,
            ];
        }

        return $array = collect($array)->sortBy("due_date")->toArray();
    }


    public function getCurrentSalesBalanceAttribute()
    {
        $creditReturnsTotal = $this->saleReturns()->sum(DB::raw('quantity * price')) - $this->saleReturns->sum('amount');

        return $this->initialBalance + $this->sales()->sum("remainder") + $this->saleDebts()->sum("service") - $this->saleDebts()->wherer("type", "discount")->sum("amount") + $this->saleDebts()->where("type", "debt")->sum("amount") + $this->saleDebts()->where("type", "debt")->sum("amount") - $this->saleDebts()->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getPastSalesBalance($date)
    {
        $creditReturnsTotal = $this->saleReturns()->where("sale_returns.due_date", "<", $date)->sum(DB::raw('quantity * price')) - $this->saleReturns->where("sale_returns.due_date", "<", $date)->sum('amount');

        return $this->initialBalance + $this->sales()->where("due_date", "<", $date)->sum("remainder") + $this->saleDebts()->where("due_date", "<", $date)->sum("service") - $this->saleDebts()->where("due_date", "<", $date)->where("type", "discount")->sum("amount") + $this->saleDebts()->where("due_date", "<", $date)->where("type", "debt")->sum("amount") + $this->saleDebts()->where("due_date", "<", $date)->sum("service") - $this->saleDebts()->where("due_date", "<", $date)->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getDaySalesBalance($date)
    {
        $initial = $this->startingDate == $date ? $this->initialBalance : 0;
        $creditReturnsTotal = $this->saleReturns()->where("sale_returns.due_date", $date)->sum(DB::raw('quantity * price')) - $this->saleReturns->where("sale_returns.due_date", $date)->sum('amount');

        return $initial + $this->sales()->where("due_date", "<", $date)->sum("remainder") + $this->saleDebts()->where("due_date", $date)->sum("service") - $this->saleDebts()->where("due_date", $date)->where("type", "discount")->sum("amount") + $this->saleDebts()->where("due_date", $date)->where("type", "debt")->sum("amount") + $this->saleDebts()->where("due_date", $date)->sum("service") - $this->saleDebts()->where("due_date", $date)->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getSalesBetweenBalance($from, $to)
    {
        $initial = ($this->startingDate >= $from && $this->startingDate <= $to) ? $this->initialBalance : 0;
        $creditReturnsTotal = $this->saleReturns()->whereBetween("due_date", [$from, $to])->sum(DB::raw('quantity * price')) - $this->saleReturns->whereBetween("due_date", [$from, $to])->sum('amount');

        return $initial + $this->sales()->whereBetween("due_date", [$from, $to])->sum("remainder") + $this->saleDebts()->whereBetween("due_date", [$from, $to])->sum("service") - $this->saleDebts()->whereBetween("due_date", [$from, $to])->where("type", "discount")->sum("amount") + $this->saleDebts()->whereBetween("due_date", [$from, $to])->where("type", "debt")->sum("amount") + $this->saleDebts()->whereBetween("due_date", [$from, $to])->sum("service") - $this->saleDebts()->whereBetween("due_date", [$from, $to])->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getCurrentPurchasesBalanceAttribute()
    {
        $creditReturnsTotal = $this->purchaseReturns()->sum(DB::raw('quantity * price')) - $this->purchaseReturns->sum('amount');

        return $this->initialBalance + $this->purchases()->sum("remainder") - $this->purchases()->where("type", "discount")->sum("amount") + $this->purchaseDebts()->where("type", "debt")->sum("amount") - $this->purchaseDebts()->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getPastPurchasesBalance($date)
    {
        $creditReturnsTotal = $this->purchaseReturns()->where("purchase_returns.due_date", "<", $date)->sum(DB::raw('quantity * price')) - $this->purchaseReturns->where("purchase_returns.due_date", "<", $date)->sum('amount');

        return $this->initialBalance + $this->purchases()->where("due_date", "<", $date)->sum("remainder") - $this->purchaseDebts()->where("due_date", "<", $date)->where("type", "discount")->sum("amount") + $this->purchaseDebts()->where("type", "debt")->where("due_date", "<", $date)->sum("amount") - $this->purchaseDebts()->where("due_date", "<", $date)->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getDayPurchasesBalance($date)
    {
        $initial = $this->startingDate == $date ? $this->initialBalance : 0;
        $creditReturnsTotal = $this->purchaseReturns()->where("purchase_returns.due_date", $date)->sum(DB::raw('quantity * price')) - $this->purchaseReturns->where("purchase_returns.due_date", $date)->sum('amount');

        return $initial + $this->purchases()->where("due_date", "<", $date)->sum("remainder") - $this->purchaseDebts()->where("due_date", $date)->where("type", "discount")->sum("amount") + $this->purchaseDebts()->where("due_date", $date)->where("type", "debt")->sum("amount") - $this->purchaseDebts()->where("due_date", $date)->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getPurchasesBetweenBalance($from, $to)
    {
        $initial = ($this->startingDate >= $from && $this->startingDate <= $to) ? $this->initialBalance : 0;
        $creditReturnsTotal = $this->purchaseReturns()->whereBetween("due_date", [$from, $to])->sum(DB::raw('quantity * price')) - $this->purchaseReturns->whereBetween("due_date", [$from, $to])->sum('amount');

        return $initial + $this->purchases()->whereBetween("due_date", [$from, $to])->sum("remainder") - $this->purchaseDebts()->whereBetween("due_date", [$from, $to])->where("type", "discount")->sum("amount") + $this->purchaseDebts()->whereBetween("due_date", [$from, $to])->where("type", "debt")->sum("amount") - $this->purchaseDebts()->whereBetween("due_date", [$from, $to])->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }
}

