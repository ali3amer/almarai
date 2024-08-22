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

    public function getMovements($id = null, $clientType = 'client')
    {
        // استعلام المبيعات
        $sales = Sale::select(
            DB::raw("'sales' as tableName"),
            DB::raw("CASE
            WHEN client_id IS NOT NULL THEN 'client'
            WHEN supplier_id IS NOT NULL THEN 'supplier'
            WHEN employee_id IS NOT NULL THEN 'employee'
         END as clientType"),
            DB::raw('null as type'),
            'sales.id as invoice_id',
            DB::raw('0 as expense'),
            DB::raw('0 as income'),
            DB::raw('0 as futureExpense'),
            DB::raw('amount as futureIncome'),
            'due_date',
            'payment',
            'bank',
            DB::raw('CONCAT("مبيعات للفاتوره رقم #", sales.id) as note'),
            DB::raw('COALESCE(client_id, supplier_id, employee_id) as owner_id'),
            DB::raw("COALESCE(clients.clientName, suppliers.supplierName, employees.employeeName) as ownerName"),
            'sales.created_at',
            'sales.updated_at'
        )->leftJoin('clients', 'clients.id', '=', 'sales.client_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'sales.supplier_id')
            ->leftJoin('employees', 'employees.id', '=', 'sales.employee_id');

        // استعلام المبيعات المدفوعة
        $paidSales = Sale::select(
            DB::raw("'sales' as tableName"),
            DB::raw("CASE
            WHEN sales.client_id IS NOT NULL THEN 'client'
            WHEN sales.supplier_id IS NOT NULL THEN 'supplier'
            WHEN sales.employee_id IS NOT NULL THEN 'employee'
         END as clientType"),
            DB::raw('null as type'),
            'sales.id as invoice_id',
            DB::raw('0 as expense'),
            DB::raw('paid as income'),
            DB::raw('0 as futureExpense'),
            DB::raw('0 as futureIncome'),
            'due_date',
            'payment',
            'bank',
            DB::raw('CONCAT("مدفوعات مبيعات لفاتوره #", sales.id) as note'),
            DB::raw('COALESCE(sales.client_id, sales.supplier_id, sales.employee_id) as owner_id'),
            DB::raw("COALESCE(clients.clientName, suppliers.supplierName, employees.employeeName) as ownerName"),
            'sales.created_at',
            'sales.updated_at'
        )->where('paid', '!=', 0)
            ->leftJoin('clients', 'clients.id', '=', 'sales.client_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'sales.supplier_id')
            ->leftJoin('employees', 'employees.id', '=', 'sales.employee_id');

        // استعلام الديون والمدفوعات
        $saleDebts = SaleDebt::select(
            DB::raw("'sale_debts' as tableName"),
            DB::raw("CASE
            WHEN client_id IS NOT NULL THEN 'client'
            WHEN supplier_id IS NOT NULL THEN 'supplier'
            WHEN employee_id IS NOT NULL THEN 'employee'
         END as clientType"),
            'type',
            DB::raw('null as invoice_id'),
            DB::raw("CASE
            WHEN type = 'debt' THEN amount
            ELSE 0
         END as expense"),
            DB::raw("CASE
            WHEN type = 'pay' THEN amount
            ELSE 0
         END as income"),
            DB::raw('0 as futureExpense'),
            DB::raw('0 as futureIncome'),
            'due_date',
            'payment',
            'bank',
            'sale_debts.note',
            DB::raw('COALESCE(client_id, supplier_id, employee_id) as owner_id'),
            DB::raw("COALESCE(clients.clientName, suppliers.supplierName, employees.employeeName) as ownerName"),
            'sale_debts.created_at',
            'sale_debts.updated_at'
        )
            ->leftJoin('clients', 'clients.id', '=', 'sale_debts.client_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'sale_debts.supplier_id')
            ->leftJoin('employees', 'employees.id', '=', 'sale_debts.employee_id');

        // استعلام مرتجعات المبيعات
        $returnSale = SaleReturn::select(
            DB::raw("'sale_returns' as tableName"),
            DB::raw("CASE
            WHEN sales.client_id IS NOT NULL THEN 'client'
            WHEN sales.supplier_id IS NOT NULL THEN 'supplier'
            WHEN sales.employee_id IS NOT NULL THEN 'employee'
         END as clientType"),
            DB::raw('null as type'),
            'sale_id as invoice_id',
            DB::raw('0 as income'),
            DB::raw('0 as expense'),
            DB::raw('quantity * price as futureExpense'),
            DB::raw('0 as futureIncome'),
            'sale_returns.due_date',
            DB::raw('null as payment'),
            DB::raw('null as bank'),
            DB::raw('CONCAT("مرتجعات مبيعات لفاتوره #", sale_id) as note'),
            DB::raw('COALESCE(sales.client_id, sales.supplier_id, sales.employee_id) as owner_id'),
            DB::raw("COALESCE(clients.clientName, suppliers.supplierName, employees.employeeName) as ownerName"),
            'sale_returns.created_at',
            'sale_returns.updated_at'
        )
            ->join('sales', 'sales.id', '=', 'sale_returns.sale_id')
            ->leftJoin('clients', 'clients.id', '=', 'sales.client_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'sales.supplier_id')
            ->leftJoin('employees', 'employees.id', '=', 'sales.employee_id');

        // استعلام المدفوعات المتعلقة بمرتجعات المبيعات
        $paidReturnSale = SaleReturn::select(
            DB::raw("'sale_returns' as tableName"),
            DB::raw("CASE
            WHEN sales.client_id IS NOT NULL THEN 'client'
            WHEN sales.supplier_id IS NOT NULL THEN 'supplier'
            WHEN sales.employee_id IS NOT NULL THEN 'employee'
         END as clientType"),
            DB::raw('null as type'),
            'sale_id as invoice_id',
            DB::raw('sale_returns.amount as income'),
            DB::raw('0 as expense'),
            DB::raw('0 as futureIncome'),
            DB::raw("0 as futureExpense"),
            'sale_returns.due_date',
            DB::raw('null as payment'),
            DB::raw('null as bank'),
            DB::raw('CONCAT("مدفوعات مرتجع لفاتوره #", sale_id) as note'),
            DB::raw('COALESCE(sales.client_id, sales.supplier_id, sales.employee_id) as owner_id'),
            DB::raw("COALESCE(clients.clientName, suppliers.supplierName, employees.employeeName) as ownerName"),
            'sale_returns.created_at',
            'sale_returns.updated_at'
        )->where("sale_returns.amount", "!=", 0)
            ->join('sales', 'sales.id', '=', 'sale_returns.sale_id')
            ->leftJoin('clients', 'clients.id', '=', 'sales.client_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'sales.supplier_id')
            ->leftJoin('employees', 'employees.id', '=', 'sales.employee_id');

        // تطبيق الفلترة عند الحاجة
        if ($id != null) {
            $sales = $sales->where($clientType . "_id", $id);
            $paidSales = $paidSales->where($clientType . "_id", $id);
            $saleDebts = $saleDebts->where($clientType . "_id", $id);
            $returnSale = $returnSale->whereHas('sale', function ($query) use ($clientType, $id) {
                $query->where($clientType . "_id", $id);
            });
            $paidReturnSale = $paidReturnSale->whereHas('sale', function ($query) use ($clientType, $id) {
                $query->where($clientType . "_id", $id);
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
