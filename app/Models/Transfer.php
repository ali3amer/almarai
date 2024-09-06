<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transfer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function getMovements()
    {
        $cash_to_bank_expense = Transfer::select(
            DB::raw("'transfers' as tableName"),
            DB::raw("null as clientType"),
            DB::raw('null as type'),
            DB::raw('null as invoice_id'),
            DB::raw('0 as income'),
            DB::raw('amount as expense'),
            DB::raw('0 as futureIncome'),
            DB::raw("0 as futureExpense"),
            'due_date',
            DB::raw("'cash' as payment"),
            'bank',
            DB::raw("IF(note IS NULL OR note = '', 'تحويل من الخزنة إلى بنك', note) as note"),
            DB::raw('null as owner_id'),
            DB::raw("null as ownerName"),
            'created_at',
            'updated_at'
        )->where("transfer_type", "cash_to_bank");

        $cash_to_bank_income = Transfer::select(
            DB::raw("'transfers' as tableName"),
            DB::raw("null as clientType"),
            DB::raw('null as type'),
            DB::raw('null as invoice_id'),
            DB::raw('amount as income'),
            DB::raw('0 as expense'),
            DB::raw('0 as futureIncome'),
            DB::raw("0 as futureExpense"),
            'due_date',
            DB::raw("'bank' as payment"),
            'bank',
            DB::raw("IF(note IS NULL OR note = '', 'إستلام بنك', note) as note"),
            DB::raw('null as owner_id'),
            DB::raw("null as ownerName"),
            'created_at',
            'updated_at'
        )->where("transfer_type", "cash_to_bank");

        $bank_to_cash_expense = Transfer::select(
            DB::raw("'transfers' as tableName"),
            DB::raw("null as clientType"),
            DB::raw('null as type'),
            DB::raw('null as invoice_id'),
            DB::raw('0 as income'),
            DB::raw('amount as expense'),
            DB::raw('0 as futureIncome'),
            DB::raw("0 as futureExpense"),
            'due_date',
            DB::raw("'bank' as payment"),
            'bank',
            DB::raw("IF(note IS NULL OR note = '', 'تحويل من بنك إلى كاش', note) as note"),
            DB::raw('null as owner_id'),
            DB::raw("null as ownerName"),
            'created_at',
            'updated_at'
        )->where("transfer_type", "bank_to_cash");

        $bank_to_cash_income = Transfer::select(
            DB::raw("'transfers' as tableName"),
            DB::raw("null as clientType"),
            DB::raw('null as type'),
            DB::raw('null as invoice_id'),
            DB::raw('amount as income'),
            DB::raw('0 as expense'),
            DB::raw('0 as futureIncome'),
            DB::raw("0 as futureExpense"),
            'due_date',
            DB::raw("'cash' as payment"),
            'bank',
            DB::raw("IF(note IS NULL OR note = '', 'استلام كاش', note) as note"),
            DB::raw('null as owner_id'),
            DB::raw("null as ownerName"),
            'created_at',
            'updated_at'
        )->where("transfer_type", "bank_to_cash");

        return $cash_to_bank_expense->union($cash_to_bank_income)
            ->union($bank_to_cash_expense)
            ->union($bank_to_cash_income)
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d h:i:s');
    }
}
