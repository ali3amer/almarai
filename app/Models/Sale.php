<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function people()
    {
        return $this->belongsTo(People::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function getMovements($id = null, $clientType = 'client')
    {
        // استعلام المبيعات
        $sales = Sale::select(
            DB::raw("'sales' as tableName"),
            DB::raw("people.type as clientType"),
            DB::raw('null as type'),
            'sales.id as invoice_id',
            'sales.id',
            DB::raw('0 as expense'),
            DB::raw('0 as income'),
            DB::raw('0 as futureExpense'),
            DB::raw('amount as futureIncome'),
            'due_date',
            'payment',
            'bank',
            'bank_id',
            DB::raw('CONCAT("مبيعات للفاتوره رقم #", sales.id) as note'),
            DB::raw('people_id as owner_id'),
            DB::raw("people.name as ownerName"),
            'sales.created_at',
            'sales.updated_at'
        )->leftJoin('people', 'people.id', '=', 'sales.people_id');

        // استعلام المبيعات المدفوعة
        $paidSales = Sale::select(
            DB::raw("'sales' as tableName"),
            DB::raw("people.type as clientType"),
            DB::raw('null as type'),
            'sales.id as invoice_id',
            'sales.id',
            DB::raw('0 as expense'),
            DB::raw('paid as income'),
            DB::raw('0 as futureExpense'),
            DB::raw('0 as futureIncome'),
            'due_date',
            'payment',
            'bank',
            'bank_id',
            DB::raw('CONCAT("مدفوعات مبيعات لفاتوره #", sales.id) as note'),
            DB::raw('people_id as owner_id'),
            DB::raw("people.name as ownerName"),
            'sales.created_at',
            'sales.updated_at'
        )->where('paid', '!=', 0)
            ->leftJoin('people', 'people.id', '=', 'sales.people_id');

        // استعلام الديون والمدفوعات
        $saleDebts = SaleDebt::select(
            DB::raw("'sale_debts' as tableName"),
            DB::raw("people.type as clientType"),
            'sale_debts.type',
            DB::raw('null as invoice_id'),
            'sale_debts.id',
            DB::raw("CASE
            WHEN sale_debts.type = 'debt' THEN amount
            ELSE 0
         END as expense"),
            DB::raw("CASE
            WHEN sale_debts.type = 'pay' THEN amount
            ELSE 0
         END as income"),
            DB::raw('0 as futureExpense'),
            DB::raw("CASE
            WHEN sale_debts.type = 'discount' THEN amount
            ELSE 0
         END as futureIncome"),
            'due_date',
            'payment',
            'bank',
            'bank_id',
            'sale_debts.note',
            DB::raw('people_id as owner_id'),
            DB::raw("people.name as ownerName"),
            'sale_debts.created_at',
            'sale_debts.updated_at'
        )
            ->leftJoin('people', 'people.id', '=', 'sale_debts.people_id');

        // استعلام مرتجعات المبيعات
        $returnSale = SaleReturn::select(
            DB::raw("'sale_returns' as tableName"),
            DB::raw("people.type as clientType"),
            DB::raw('null as type'),
            'sale_id as invoice_id',
            'sale_returns.id',
            DB::raw('0 as income'),
            DB::raw('0 as expense'),
            DB::raw('quantity * price as futureExpense'),
            DB::raw('0 as futureIncome'),
            'sale_returns.due_date',
            DB::raw("'cash' as payment"),
            DB::raw('null as bank'),
            DB::raw('null as bank_id'),
            DB::raw('CONCAT("مرتجعات مبيعات لفاتوره #", sale_id) as note'),
            DB::raw('people_id as owner_id'),
            DB::raw("people.name as ownerName"),
            'sale_returns.created_at',
            'sale_returns.updated_at'
        )
            ->join('sales', 'sales.id', '=', 'sale_returns.sale_id')
            ->leftJoin('people', 'people.id', '=', 'sales.people_id');

        // استعلام المدفوعات المتعلقة بمرتجعات المبيعات
        $paidReturnSale = SaleReturn::select(
            DB::raw("'sale_returns' as tableName"),
            DB::raw("people.type as clientType"),
            DB::raw('null as type'),
            'sale_id as invoice_id',
            'sale_returns.id',
            DB::raw('sale_returns.amount as income'),
            DB::raw('0 as expense'),
            DB::raw('0 as futureIncome'),
            DB::raw("0 as futureExpense"),
            'sale_returns.due_date',
            DB::raw("'cash' as payment"),
            DB::raw('null as bank'),
            DB::raw('null as bank_id'),
            DB::raw('CONCAT("مدفوعات مرتجع لفاتوره #", sale_id) as note'),
            DB::raw('people_id as owner_id'),
            DB::raw("people.name as ownerName"),
            'sale_returns.created_at',
            'sale_returns.updated_at'
        )->where("sale_returns.amount", "!=", 0)
            ->join('sales', 'sales.id', '=', 'sale_returns.sale_id')
            ->leftJoin('people', 'people.id', '=', 'sales.people_id');

        // تطبيق الفلترة عند الحاجة
        if ($id != null) {
            $sales = $sales->where("people_id", $id);
            $paidSales = $paidSales->where("people_id", $id);
            $saleDebts = $saleDebts->where("people_id", $id);
            $returnSale = $returnSale->whereHas('sale', function ($query) use ($id) {
                $query->where("people_id", $id);
            });
            $paidReturnSale = $paidReturnSale->whereHas('sale', function ($query) use ($id) {
                $query->where("people_id", $id);
            });
        }

        // دمج النتائج
        return $sales->union($paidSales)
            ->union($saleDebts)
            ->union($returnSale)
            ->union($paidReturnSale)
            ->orderBy('due_date', 'asc')
            ->get();
    }


}
