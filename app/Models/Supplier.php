<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Supplier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function saleReturns()
    {
        return $this->hasManyThrough(SaleReturn::class, Sale::class);
    }

    public function saleDebts()
    {
        return $this->hasMany(SaleDebt::class);
    }

    public function purchaseDebts()
    {
        return $this->hasMany(PurchaseDebt::class);
    }

    public function purchaseReturns()
    {
        return $this->hasManyThrough(PurchaseReturn::class, Purchase::class);
    }

    public function getMovements()
    {
        $purchases = Purchase::select(
            DB::raw("'purchases' as tableName"),
            DB::raw("'supplier' as clientType"),
            DB::raw('null as type'),
            'id as invoice_id',
            'amount as transaction_amount',
            'paid as transaction_paid',
            'remainder as transaction_remainder',
            'discount as transaction_discount',
            DB::raw('null as transaction_service'),
            'due_date as transaction_date',
            'payment',
            'bank',
            DB::raw('CONCAT("مشتريات للفاتوره رقم #", id) as note'),
            'created_at',
            'updated_at'
        )->where('supplier_id', $this->id);

        $remainingPurchases = Sale::select(
            DB::raw("'purchases' as tableName"),
            DB::raw("'supplier' as clientType"),
            DB::raw('null as type'),
            'id as invoice_id',
            DB::raw('remainder as transaction_amount'),
            DB::raw('0 as transaction_paid'),
            'remainder as transaction_remainder',
            DB::raw('null as transaction_discount'),
            DB::raw('null as transaction_service'),
            'due_date as transaction_date',
            'payment',
            'bank',
            DB::raw('CONCAT("متبقي مشتريات لفاتوره #", id) as note'),
            'created_at',
            'updated_at'
        )->where('client_id', $this->id)
            ->where('remainder', '!=', 0);


        $purchaseDebts = PurchaseDebt::select(
            DB::raw("'purchase_debts' as tableName"),
            DB::raw("'supplier' as clientType"),
            'type',
            DB::raw('null as invoice_id'),
            'amount as transaction_amount',
            DB::raw('null as transaction_paid'),
            DB::raw('null as transaction_remainder'),
            'discount as transaction_discount',
            'service as transaction_service',
            'due_date as transaction_date',
            'payment',
            'bank',
            'note',
            'created_at',
            'updated_at'
        )->where('supplier_id', $this->id);

        $returnPurchase = PurchaseReturn::select(
            DB::raw("'purchase_returns' as tableName"),
            DB::raw("'supplier' as clientType"),
            DB::raw('null as type'),
            'purchase_id as invoice_id',
            DB::raw("quantity * price as transaction_amount"),
            'amount as transaction_paid',
            DB::raw('null as transaction_remainder'),
            DB::raw('null as transaction_discount'),
            DB::raw('null as transaction_service'),
            'due_date as transaction_date',
            DB::raw('null as payment'),
            DB::raw('null as bank'),
            DB::raw('CONCAT("مرتجعات مشتريا لفاتوره #", purchase_id) as note'),
            'created_at',
            'updated_at'
        )->whereHas('purchase', function ($query) {
            $query->where("supplier_id", $this->id);
        });

        return $purchases->union($remainingPurchases)->union($purchaseDebts)->union($returnPurchase)
            ->orderBy('transaction_date', 'asc')
            ->get();
    }

    public function getSalesMovements()
    {
        $sales = Sale::select(
            DB::raw("'sales' as tableName"),
            DB::raw("'supplier' as clientType"),
            DB::raw('null as type'),
            'id as invoice_id',
            'amount as transaction_amount',
            'paid as transaction_paid',
            'remainder as transaction_remainder',
            'discount as transaction_discount',
            DB::raw('null as transaction_service'),
            'due_date as transaction_date',
            'payment',
            'bank',
            DB::raw('CONCAT("مبيعات للفاتوره رقم #", id) as note'),
            'created_at',
            'updated_at'
        )->where('supplier_id', $this->id);

        $remainingSales = Sale::select(
            DB::raw("'sales' as tableName"),
            DB::raw("'client' as clientType"),
            DB::raw('null as type'),
            'id as invoice_id',
            DB::raw('remainder as transaction_amount'),
            DB::raw('0 as transaction_paid'),
            'remainder as transaction_remainder',
            DB::raw('null as transaction_discount'),
            DB::raw('null as transaction_service'),
            'due_date as transaction_date',
            'payment',
            'bank',
            DB::raw('CONCAT("متبقي مبيعات لفاتوره #", id) as note'),
            'created_at',
            'updated_at'
        )->where('client_id', $this->id)
            ->where('remainder', '!=', 0);

        $saleDebts = SaleDebt::select(
            DB::raw("'sale_debts' as tableName"),
            DB::raw("'supplier' as clientType"),
            'type',
            DB::raw('null as invoice_id'),
            'amount as transaction_amount',
            DB::raw('null as transaction_paid'),
            DB::raw('null as transaction_remainder'),
            'discount as transaction_discount',
            'service as transaction_service',
            'due_date as transaction_date',
            'payment',
            'bank',
            'note',
            'created_at',
            'updated_at'
        )->where('supplier_id', $this->id);

        $returnSale = SaleReturn::select(
            DB::raw("'sale_returns' as tableName"),
            DB::raw("'supplier' as clientType"),
            DB::raw('null as type'),
            'sale_id as invoice_id',
            DB::raw("quantity * price as transaction_amount"),
            'amount as transaction_paid',
            DB::raw('null as transaction_remainder'),
            DB::raw('null as transaction_discount'),
            DB::raw('null as transaction_service'),
            'due_date as transaction_date',
            DB::raw('null as payment'),
            DB::raw('null as bank'),
            DB::raw('CONCAT("مرتجعات مبيعات لفاتوره #", sale_id) as note'),
            'created_at',
            'updated_at'
        )->whereHas('sale', function ($query) {
            $query->where("supplier_id", $this->id);
        });

        return $sales->union($remainingSales)->union($saleDebts)->union($returnSale)
            ->orderBy('transaction_date', 'asc')
            ->get();
    }

    public function getCurrentBalanceAttribute()
    {
        $creditReturnsTotal = $this->purchaseReturns()->sum(DB::raw('quantity * price')) - $this->purchaseReturns->sum('amount');

        return $this->initialBalance + $this->purchases()->sum("remainder") + $this->purchaseDebts()->where("type", "debt")->sum("amount") - $this->purchaseDebts()->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getCurrentSalesBalanceAttribute()
    {
        $creditReturnsTotal = $this->saleReturns()->sum(DB::raw('quantity * price')) - $this->saleReturns->sum('amount');

        return $this->initialSaleBalance + $this->sales()->sum("remainder") + $this->saleDebts()->where("type", "debt")->sum("amount") - $this->saleDebts()->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getPastBalance($date)
    {
        $creditReturnsTotal = $this->purchaseReturns()->where("purchase_returns.due_date", "<", $date)->sum(DB::raw('quantity * price')) - $this->purchaseReturns->where("purchase_returns.due_date", "<", $date)->sum('amount');

        return $this->initialBalance + $this->purchases()->where("due_date", "<", $date)->sum("remainder") + $this->purchaseDebts()->where("type", "debt")->where("due_date", "<", $date)->sum("amount") - $this->purchaseDebts()->where("due_date", "<", $date)->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }

    public function getSalesPastBalance($date)
    {
        $creditReturnsTotal = $this->saleReturns()->where("sale_returns.due_date", "<", $date)->sum(DB::raw('quantity * price')) - $this->saleReturns->where("sale_returns.due_date", "<", $date)->sum('amount');

        return $this->initialBalance + $this->sales()->where("due_date", "<", $date)->sum("remainder") + $this->saleDebts()->where("type", "debt")->where("due_date", "<", $date)->sum("amount") - $this->saleDebts()->where("due_date", "<", $date)->where("type", "pay")->sum("amount") - $creditReturnsTotal;
    }
}
