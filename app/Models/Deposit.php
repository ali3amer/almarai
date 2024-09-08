<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Deposit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function depositDebts()
    {
        return $this->hasMany(DepositDebt::class);
    }

    public function getMovements()
    {

        $deposits = DepositDebt::select(
            DB::raw("'deposits' as tableName"),
            DB::raw("null as clientType"),
            'type',
            DB::raw('null as invoice_id'),
            'deposit_debts.id',
            DB::raw("CASE
        WHEN type = 'pay' THEN amount
        ELSE 0
     END as income"),
            DB::raw("CASE
        WHEN type = 'debt' THEN amount
        ELSE 0
     END as expense"),
            DB::raw('0 as futureExpense'),
            DB::raw('0 as futureIncome'),
            'due_date',
            'payment',
            'bank',
            'bank_id',
            DB::raw('deposit_debts.note as note'),
            DB::raw('deposit_id as owner_id'),
            DB::raw("deposits.name as ownerName"),
            'deposit_debts.created_at',
            'deposit_debts.updated_at'
        )
            ->leftJoin('deposits', 'deposits.id', '=', 'deposit_debts.deposit_id')->orderBy('due_date', 'asc')->get();

        return $deposits;
    }

    public function getCurrentBalanceAttribute()
    {
        return $this->initialBalance + $this->depositDebts()->where("type", "pay")->sum("amount") - $this->depositDebts()->where("type", "debt")->sum("amount");
    }

    public function getPastBalance($date)
    {
        return $this->initialBalance + $this->depositDebts()->where("type", "pay")->where("due_date", "<", $date)->sum("amount") - $this->depositDebts()->where("type", "debt")->where("due_date", "<", $date)->sum("amount");
    }

    public function getDayBalance($date)
    {
        $initial = $this->startingDate == $date ? $this->initialBalance : 0;
        return $initial + $this->depositDebts()->where("type", "pay")->where("due_date", $date)->sum("amount") - $this->depositDebts()->where("type", "debt")->where("due_date", $date)->sum("amount");
    }

    public function getBetweenBalance($from, $to)
    {
        $initial = ($this->startingDate >= $from && $this->startingDate <= $to) ? $this->initialBalance : 0;
        return $initial + $this->depositDebts()->where("type", "pay")->whereBetween("due_date", [$from, $to])->sum("amount") - $this->depositDebts()->where("type", "debt")->whereBetween("due_date", [$from, $to])->sum("amount");
    }
}
