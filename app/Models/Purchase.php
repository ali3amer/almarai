<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    public function people()
    {
        return $this->belongsTo(People::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function purchaseDebts()
    {
        return $this->hasMany(PurchaseDebt::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function getMovements($id = null, $clientType = 'supplier')
    {
        $purchases = Purchase::select(
            DB::raw("'purchases' as tableName"),
            DB::raw("people.type as clientType"),
            DB::raw('null as type'),
            'purchases.id as invoice_id',
            'purchases.id',
            DB::raw('0 as expense'),
            DB::raw('0 as income'),
            DB::raw('0 as futureIncome'),
            DB::raw('amount as futureExpense'),
            'due_date',
            'payment',
            'bank',
            'bank_id',
            DB::raw('CONCAT("مشتريات للفاتوره رقم #", purchases.id) as note'),
            DB::raw('people_id as owner_id'),
            DB::raw("people.name as ownerName"),
            'purchases.created_at',
            'purchases.updated_at'
        )->leftJoin('people', 'people.id', '=', 'purchases.people_id');

        $paidPurchases = Purchase::select(
            DB::raw("'purchases' as tableName"),
            DB::raw("people.type as clientType"),
            DB::raw('null as type'),
            'purchases.id as invoice_id',
            'purchases.id',
            DB::raw('paid as expense'),
            DB::raw('0 as income'),
            DB::raw('0 as futureExpense'),
            DB::raw('0 as futureIncome'),
            'due_date',
            'payment',
            'bank',
            'bank_id',
            DB::raw('CONCAT("مدفوعات مشتريات لفاتوره #", purchases.id) as note'),
            DB::raw('people_id as owner_id'),
            DB::raw("people.name as ownerName"),
            'purchases.created_at',
            'purchases.updated_at'
        )->where('paid', '!=', 0)
            ->leftJoin('people', 'people.id', '=', 'purchases.people_id');

        $purchaseDebts = PurchaseDebt::select(
            DB::raw("'purchase_debts' as tableName"),
            DB::raw("people.type as clientType"),
            'purchase_debts.type',
            DB::raw('null as invoice_id'),
            'purchase_debts.id',
            DB::raw("CASE
        WHEN purchase_debts.type = 'debt' THEN amount
        ELSE 0
     END as income"),
            DB::raw("CASE
        WHEN purchase_debts.type = 'pay' THEN amount
        ELSE 0
     END as expense"),
            DB::raw("CASE
        WHEN purchase_debts.type = 'discount' THEN amount
        ELSE 0
     END as futureExpense"),
            DB::raw('0 as futureIncome'),
            'due_date',
            'payment',
            'bank',
            'bank_id',
            'purchase_debts.note',
            DB::raw('people_id as owner_id'),
            DB::raw("people.name as ownerName"),
            'purchase_debts.created_at',
            'purchase_debts.updated_at'
        )
            ->leftJoin('people', 'people.id', '=', 'purchase_debts.people_id');

        $returnPurchase = PurchaseReturn::select(
            DB::raw("'purchase_returns' as tableName"),
            DB::raw("people.type as clientType"),
            DB::raw('null as type'),
            'purchase_id as invoice_id',
            'purchase_returns.id',
            DB::raw('0 as income'),
            DB::raw('0 as expense'),
            DB::raw('quantity * price as futureIncome'),
            DB::raw("0 as futureExpense"),
            'purchase_returns.due_date',
            DB::raw("'cash' as payment"),
            DB::raw('null as bank'),
            DB::raw('null as bank_id'),
            DB::raw('CONCAT("مرتجعات مشتريات لفاتوره #", purchase_id) as note'),
            DB::raw('people_id as owner_id'),
            DB::raw("people.name as ownerName"),
            'purchase_returns.created_at',
            'purchase_returns.updated_at'
        )
            ->join('purchases', 'purchases.id', '=', 'purchase_returns.purchase_id')
            ->leftJoin('people', 'people.id', '=', 'purchases.people_id');

        $paidReturnPurchase = PurchaseReturn::select(
            DB::raw("'purchase_returns' as tableName"),
            DB::raw("people.type as clientType"),
            DB::raw('null as type'),
            'purchase_id as invoice_id',
            'purchase_returns.id',
            DB::raw('0 as expense'),
            DB::raw('purchase_returns.amount as income'),
            DB::raw('0 as futureIncome'),
            DB::raw("0 as futureExpense"),
            'purchase_returns.due_date',
            DB::raw("'cash' as payment"),
            DB::raw('null as bank'),
            DB::raw('null as bank_id'),
            DB::raw('CONCAT("مدفوعات مرتجع لفاتوره #", purchase_id) as note'),
            DB::raw('people_id as owner_id'),
            DB::raw("people.name as ownerName"), // إضافة اسم المالك
            'purchase_returns.created_at',
            'purchase_returns.updated_at'
        )->where("purchase_returns.amount", "!=", 0)
            ->join('purchases', 'purchases.id', '=', 'purchase_returns.purchase_id')
            ->leftJoin('people', 'people.id', '=', 'purchases.people_id');

        if ($id != null) {
            $purchases = $purchases->where("people_id", $id);

            $paidPurchases = $paidPurchases->where("people_id", $id);

            $purchaseDebts = $purchaseDebts->where("people_id", $id);

            $returnPurchase = $returnPurchase->whereHas('purchase', function ($query) use ($id) {
                $query->where("people_id", $id);
            });

            $paidReturnPurchase = $paidReturnPurchase->whereHas('purchase', function ($query) use ($id) {
                $query->where("people_id", $id);
            });
        }


        return $purchases->union($paidPurchases)
            ->union($purchaseDebts)
            ->union($returnPurchase)
            ->union($paidReturnPurchase)
            ->orderBy('due_date', 'asc')
            ->get();
    }

}
