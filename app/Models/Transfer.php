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
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function getMovements($id = null, $duration = null, $from = null, $to = null)
    {

        $array = [];

        if ($duration == "day") {
            $transfers = Transfer::where("due_date", $from)->get();

        } elseif ($duration == "duration") {
            $transfers = Transfer::whereBetween("due_date", [$from, $to])->get();
        } else {
            $transfers = Transfer::all();
        }

        foreach ($transfers as $transfer) {
            $array[] = [
                "tableName" => "transfers",
                "clientType" => null,
                "type" => null,
                "real" => true,
                "invoice_id" => null,
                "id" => $transfer->id,
                "debit" => $transfer->amount,
                "credit" => 0,
                "due_date" => $transfer->due_date,
                "payment" => $transfer->transfer_type == "cash_to_bank" ? "cash" : "bank",
                "bank" => $transfer->bank,
                "bank_id" => $transfer->bank_id,
                "note" => $transfer->note != null || $transfer->note != '' ? $transfer->note : ($transfer->transfer_type == "cash_to_bank" ? "تحويل من الخزنة إلى بنك" : "تحويل من البنك إلى الخزنة"),
                "owner_id" => null,
                "ownerName" => null,
                "created_at" => $transfer->created_at,
                "updated_at" => $transfer->updated_at,
            ];
            $array[] = [
                "tableName" => "transfers",
                "clientType" => null,
                "type" => null,
                "real" => true,
                "invoice_id" => null,
                "id" => $transfer->id,
                "debit" => 0,
                "credit" => $transfer->amount,
                "due_date" => $transfer->due_date,
                "payment" => $transfer->transfer_type == "cash_to_bank" ? "bank" : "cash",
                "bank" => $transfer->bank,
                "bank_id" => $transfer->bank_id,
                "note" => $transfer->note ?? ($transfer->transfer_type == "cash_to_bank" ?  "إستلام بنك" : "إستلام كاش"),
                "owner_id" => null,
                "ownerName" => null,
                "created_at" => $transfer->created_at,
                "updated_at" => $transfer->updated_at,
            ];

        }

        return $array = collect($array)->sortBy("due_date")->toArray();

    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d h:i:s');
    }
}
