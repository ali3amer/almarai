<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    public function people()
    {
        return $this->belongsTo(People::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function purchaseDebts()
    {
        return $this->hasMany(PurchaseDebt::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function getMovements($id = null, $duration = null, $from = null, $to = null)
    {
        $array = [];

        if ($duration == "day") {
            $purchases = Purchase::where("due_date", $from)->get();
            $purchaseDebts = PurchaseDebt::where("due_date", $from)->get();
            $purchaseReturns = PurchaseReturn::where('due_date', $from)->get();
        } elseif ($duration == "duration") {
            $purchases = Purchase::whereBetween("due_date", [$from, $to])->get();
            $purchaseDebts = PurchaseDebt::whereBetween("due_date", [$from, $to])->get();
            $purchaseReturns = PurchaseReturn::whereBetween('due_date', [$from, $to])->get();
        } else {
            $purchases = Purchase::all();
            $purchaseDebts = PurchaseDebt::all();
            $purchaseReturns = PurchaseReturn::all();
        }


        if ($id != null) {
            $purchases = $purchases->where("people_id", $id);

            $purchaseDebts = $purchaseDebts->where("people_id", $id);

            $purchaseReturns = $purchaseReturns->filter(function ($return) use ($id) {
                return $return->purchase->people_id == $id;
            });
        }
        foreach ($purchases as $purchase) {
            $array[] = [
                "tableName" => "purchases",
                "clientType" => $purchase->people->type,
                "type" => null,
                "real" => false,
                "invoice_id" => $purchase->id,
                "id" => $purchase->id,
                "debit" => 0,
                "credit" => $purchase->amount,
                "due_date" => $purchase->due_date,
                "payment" => null,
                "bank" => null,
                "bank_id" => null,
                "note" => "فاتورة مشتريات رقم #" . $purchase->id,
                "owner_id" => $purchase->people_id,
                "ownerName" => $purchase->people->name,
                "created_at" => $purchase->created_at,
                "updated_at" => $purchase->updated_at,
            ];

            if ($purchase->paid != 0) {
                $array[] = [
                    "tableName" => "purchases",
                    "clientType" => $purchase->people->type,
                    "type" => null,
                    "real" => true,
                    "invoice_id" => $purchase->id,
                    "id" => $purchase->id,
                    "debit" => $purchase->paid,
                    "credit" => 0,
                    "due_date" => $purchase->due_date,
                    "payment" => $purchase->payment,
                    "bank" => $purchase->bank,
                    "bank_id" => $purchase->bank_id,
                    "note" => "مدفوعات فاتورة مشتريات رقم #" . $purchase->id,
                    "owner_id" => $purchase->people_id,
                    "ownerName" => $purchase->people->name,
                    "created_at" => $purchase->created_at,
                    "updated_at" => $purchase->updated_at,
                ];
            }
        }

        foreach ($purchaseReturns as $return) {
            $array[] = [
                "tableName" => "purchase_returns",
                "clientType" => $return->purchase->people->type,
                "type" => null,
                "real" => false,
                "invoice_id" => $return->purchase_id,
                "id" => $return->id,
                "debit" => $return->price * $return->quantity,
                "credit" => 0,
                "due_date" => $return->due_date,
                "payment" => null,
                "bank" => $return->bank,
                "bank_id" => $return->bank_id,
                "note" => "مرتجعات فاتورة مشتريات رقم #" . $return->purchase_id,
                "owner_id" => $return->purchase->people_id,
                "ownerName" => $return->purchase->people->name,
                "created_at" => $return->created_at,
                "updated_at" => $return->updated_at,
            ];
            if ($return->amount != 0) {
                $array[] = [
                    "tableName" => "purchase_returns",
                    "clientType" => $return->purchase->people->type,
                    "type" => null,
                    "real" => true,
                    "invoice_id" => $return->purchase_id,
                    "id" => $return->id,
                    "debit" => 0,
                    "credit" => $return->amount,
                    "due_date" => $return->due_date,
                    "payment" => $return->payment,
                    "bank" => $return->bank,
                    "bank_id" => $return->bank_id,
                    "note" => "مدفوعات مرتجعات فاتورة مشتريات رقم #" . $return->purchase_id,
                    "owner_id" => $return->purchase->people_id,
                    "ownerName" => $return->purchase->people->name,
                    "created_at" => $return->created_at,
                    "updated_at" => $return->updated_at,
                ];
            }
        }

        foreach ($purchaseDebts as $debt) {
            $array[] = [
                "tableName" => "purchase_debts",
                "clientType" => $debt->people->type,
                "type" => $debt->type,
                "real" => !($debt->type == "discount"),
                "invoice_id" => null,
                "id" => $debt->id,
                "debit" => $debt->type == "pay" || $debt->type == "discount" ? $debt->amount : 0,
                "credit" => $debt->type == "debt" ? $debt->amount : 0,
                "due_date" => $debt->due_date,
                "payment" => $debt->type != "discount" ? $debt->payment : null,
                "bank" => $debt->bank,
                "bank_id" => $debt->bank_id,
                "note" => $debt->note,
                "owner_id" => $debt->people_id,
                "ownerName" => $debt->people->name,
                "created_at" => $debt->created_at,
                "updated_at" => $debt->updated_at,
            ];
        }

        return $array = collect($array)->sortBy("due_date")->toArray();
    }

}
