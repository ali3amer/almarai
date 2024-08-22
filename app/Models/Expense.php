<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Expense extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function option()
    {
        return $this->belongsTo(ExpenseOption::class);
    }

    public function getMovements()
    {
        $expenses = Expense::select(
            DB::raw("'expenses' as tableName"),
            DB::raw("null as clientType"),
            DB::raw('null as type'),
            DB::raw('null as invoice_id'),
            DB::raw('0 as income'),
            DB::raw('amount as expense'),
            DB::raw('0 as futureIncome'),
            DB::raw("0 as futureExpense"),
            'due_date',
            'payment',
            'bank',
            'description as note',
            DB::raw('null as owner_id'),
            DB::raw("expense_options.optionName as ownerName"),
            'expenses.created_at',
            'expenses.updated_at'
        )
            ->join('expense_options', 'expense_options.id', '=', 'expenses.option_id')->orderBy('due_date', 'asc')->get();


        return $expenses;
    }


    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d h:i:s');
    }
}
