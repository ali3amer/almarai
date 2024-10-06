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

    public function getMovements($id = null, $duration = null, $from = null, $to = null)
    {
        $array = [];

        if ($duration == "day") {
            $withdraws = Withdraw::where("due_date", $from)->get();

        } elseif ($duration == "duration") {
            $withdraws = Withdraw::whereBetween("due_date", [$from, $to])->get();
        } else {
            $withdraws = Withdraw::all();
        }

        foreach ($withdraws as $withdraw) {
            $array[] = [
                "tableName" => "withdraws",
                "clientType" => null,
                "type" => null,
                "real" => true,
                "invoice_id" => null,
                "id" => $withdraw->id,
                "debit" => 0,
                "credit" => $withdraw->amount,
                "due_date" => $withdraw->due_date,
                "payment" => $withdraw->payment,
                "bank" => $withdraw->bank,
                "bank_id" => $withdraw->bank_id,
                "note" => $withdraw->note ?? "تم السحب من الخزنه",
                "owner_id" => null,
                "ownerName" => "الخزنه",
                "created_at" => $withdraw->created_at,
                "updated_at" => $withdraw->updated_at,
            ];
            $array[] = [
                "tableName" => "withdraws",
                "clientType" => null,
                "type" => null,
                "real" => true,
                "invoice_id" => null,
                "id" => $withdraw->id,
                "debit" => $withdraw->amount,
                "credit" => 0,
                "due_date" => $withdraw->due_date,
                "payment" => $withdraw->payment,
                "bank" => $withdraw->bank,
                "bank_id" => $withdraw->bank_id,
                "note" => $withdraw->note ?? "تم التوريد الى الخزنه",
                "owner_id" => null,
                "ownerName" => "الخزنه",
                "created_at" => $withdraw->created_at,
                "updated_at" => $withdraw->updated_at,
            ];
        }

        return $array = collect($array)->sortBy("created_at")->toArray();
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
