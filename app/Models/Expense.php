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

    public function getMovements($id = null, $duration = null, $from = null, $to = null)
    {
        $array = [];

        if ($duration == "day") {
            $expenses = Expense::where("due_date", $from)->get();

        } elseif ($duration == "duration") {
            $expenses = Expense::whereBetween("due_date", [$from, $to])->get();
        } else {
            $expenses = Expense::all();
        }

        foreach ($expenses as $expense) {
            $array[] = [
                "tableName" => "expenses",
                "clientType" => null,
                "type" => null,
                "real" => true,
                "invoice_id" => null,
                "id" => $expense->id,
                "debit" => $expense->amount,
                "credit" => 0,
                "due_date" => $expense->due_date,
                "payment" => $expense->payment,
                "bank" => $expense->bank,
                "bank_id" => $expense->bank_id,
                "note" => $expense->note,
                "owner_id" => null,
                "ownerName" => $expense->option_id != null ? $expense->option->optionName : null,
                "created_at" => $expense->created_at,
                "updated_at" => $expense->updated_at,
            ];
        }

        return $array = collect($array)->sortBy("due_date")->toArray();
    }



    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d h:i:s');
    }
}
