<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Withdraw extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function getMovements()
    {
        $withdraw = Withdraw::select(
            DB::raw("'withdraws' as tableName"),
            DB::raw("null as clientType"),
            DB::raw('null as type'),
            DB::raw('null as invoice_id'),
            DB::raw('amount as income'),
            DB::raw('0 as expense'),
            DB::raw('0 as futureIncome'),
            DB::raw("0 as futureExpense"),
            'due_date',
            'payment',
            DB::raw('null as bank'),
            DB::raw('null as bank_id'),
            DB::raw('"تم السحب من الخزنه" as note'),
            DB::raw('null as owner_id'),
            DB::raw("'الخزنه' as ownerName"),
            'created_at',
            'updated_at'
        )->orderBy('due_date', 'asc')->get();


        return $withdraw;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d h:i:s');
    }
}
