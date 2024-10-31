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

    public function getMovements($id = null, $duration = null, $from = null, $to = null)
    {
        $array = [];

        if ($duration == "day") {
            $deposits = DepositDebt::where("due_date", $from)->get();

        } elseif ($duration == "duration") {
            $deposits = DepositDebt::whereBetween("due_date", [$from, $to])->get();
        } else {
            $deposits = DepositDebt::all();
        }


        if ($id != null) {
            $deposits = $deposits->where("people_id", $id);
        }
        foreach ($deposits as $deposit) {
            $array[] = [
                "tableName" => "deposit_debts",
                "clientType" => $deposit->people->type,
                "type" => $deposit->type,
                "real" => true,
                "invoice_id" => null,
                "id" => $deposit->id,
                "debit" => $deposit->type == "debt" ? $deposit->amount : 0,
                "credit" => $deposit->type == "pay" ? $deposit->amount : 0,
                "due_date" => $deposit->due_date,
                "payment" => $deposit->payment,
                "bank" => $deposit->bank,
                "bank_id" => $deposit->bank_id,
                "note" => $deposit->note,
                "owner_id" => $deposit->people_id,
                "ownerName" => $deposit->people->name,
                "created_at" => $deposit->created_at,
                "updated_at" => $deposit->updated_at,
            ];
        }

        return $array = collect($array)->sortBy("due_date")->toArray();
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
