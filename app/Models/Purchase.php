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


    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function purchaseDebts()
    {
        return $this->hasMany(PurchaseDebt::class);
    }

    public function getMovements($id = null, $clientType = 'supplier')
    {
        $purchases = Purchase::select(
            DB::raw("'purchases' as tableName"),
            DB::raw("'supplier' as clientType"),
            DB::raw('null as type'),
            'purchases.id as invoice_id',
            DB::raw('0 as debt'),
            DB::raw('0 as paid'),
            DB::raw('0 as futureDebt'),
            DB::raw('amount as futurePaid'),
            'due_date',
            'payment',
            'bank',
            DB::raw('CONCAT("مشتريات للفاتوره رقم #", purchases.id) as note'),
            DB::raw('purchases.supplier_id as owner_id'),
            DB::raw("suppliers.supplierName as ownerName"),
            'purchases.created_at',
            'purchases.updated_at'
        )->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id');

        $paidPurchases = Purchase::select(
            DB::raw("'purchases' as tableName"),
            DB::raw("'supplier' as clientType"),
            DB::raw('null as type'),
            'purchases.id as invoice_id',
            DB::raw('0 as debt'),
            'paid',
            DB::raw('0 as futureDebt'),
            DB::raw('0 as futurePaid'),
            'due_date',
            'payment',
            'bank',
            DB::raw('CONCAT("مدفوعات مشتريات لفاتوره #", purchases.id) as note'),
            DB::raw('purchases.supplier_id as owner_id'),
            DB::raw("suppliers.supplierName as ownerName"),
            'purchases.created_at',
            'purchases.updated_at'
        )->where('paid', '!=', 0)
            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id');

        $purchaseDebts = PurchaseDebt::select(
            DB::raw("'purchase_debts' as tableName"),
            DB::raw("'supplier' as clientType"),
            'type',
            DB::raw('null as invoice_id'),
            DB::raw("CASE
        WHEN type = 'debt' THEN amount
        ELSE 0
     END as debt"),
            DB::raw("CASE
        WHEN type = 'pay' THEN amount
        ELSE 0
     END as paid"),
            DB::raw('0 as futureDebt'),
            DB::raw('0 as futurePaid'),
            'due_date',
            'payment',
            'bank',
            'purchase_debts.note',
            DB::raw('supplier_id as owner_id'),
            DB::raw("suppliers.supplierName as ownerName"),
            'purchase_debts.created_at',
            'purchase_debts.updated_at'
        )
            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchase_debts.supplier_id');

        $returnPurchase = PurchaseReturn::select(
            DB::raw("'purchase_returns' as tableName"),
            DB::raw("'supplier' as clientType"),
            DB::raw('null as type'),
            'purchase_id as invoice_id',
            DB::raw('0 as paid'),
            DB::raw('0 as debt'),
            DB::raw('0 as futurePaid'),
            DB::raw("quantity * price as futureDebt"),
            'purchase_returns.due_date',
            DB::raw('null as payment'),
            DB::raw('null as bank'),
            DB::raw('CONCAT("مرتجعات مشتريات لفاتوره #", purchase_id) as note'),
            DB::raw('purchases.supplier_id as owner_id'),
            DB::raw("suppliers.supplierName as owner_name"),
            'purchase_returns.created_at',
            'purchase_returns.updated_at'
        )
            ->join('purchases', 'purchases.id', '=', 'purchase_returns.purchase_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id');

        $paidReturnPurchase = PurchaseReturn::select(
            DB::raw("'purchase_returns' as tableName"),
            DB::raw("'supplier' as clientType"),
            DB::raw('null as type'),
            'purchase_id as invoice_id',
            DB::raw('purchase_returns.amount as paid'),
            DB::raw('0 as debt'),
            DB::raw('0 as futurePaid'),
            DB::raw("0 as futureDebt"),
            'purchase_returns.due_date',
            DB::raw('null as payment'),
            DB::raw('null as bank'),
            DB::raw('CONCAT("مدفوعات مرتجع لفاتوره #", purchase_id) as note'),
            DB::raw('purchases.supplier_id as owner_id'),
            DB::raw("suppliers.supplierName as ownerName"),  // إضافة اسم المالك
            'purchase_returns.created_at',
            'purchase_returns.updated_at'
        )->where("purchase_returns.amount", "!=", 0)->join('purchases', 'purchases.id', '=', 'purchase_returns.purchase_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id');

        if ($id != null) {
            $purchases = $purchases->where($clientType . "_id", $id);

            $paidPurchases = $paidPurchases->where($clientType . "_id", $id);

            $purchaseDebts = $purchaseDebts->where($clientType . "_id", $id);

            $returnPurchase = $returnPurchase->whereHas('purchase', function ($query) use ($clientType, $id) {
                $query->where($clientType . "_id", $id);
            });

            $paidReturnPurchase = $paidReturnPurchase->whereHas('purchase', function ($query) use ($clientType, $id) {
                $query->where($clientType . "_id", $id);
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
