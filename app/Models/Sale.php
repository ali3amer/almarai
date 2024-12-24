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

    public function people()
    {
        return $this->belongsTo(People::class);
    }

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

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function getMovements($id = null, $duration = null, $from = null, $to = null)
    {
        $array = [];

        if ($duration == "day") {
            $sales = Sale::where("due_date", $from)->get();
            $saleDebts = SaleDebt::where("due_date", $from)->get();
            $saleReturns = SaleReturn::where("due_date", $from)->get();
        } elseif ($duration == "duration") {
            $sales = Sale::whereBetween("due_date", [$from, $to])->get();
            $saleDebts = SaleDebt::whereBetween("due_date", [$from, $to])->get();
            $saleReturns = SaleReturn::whereBetween("due_date", [$from, $to])->get();
        } else {
            $sales = Sale::all();
            $saleDebts = SaleDebt::all();
            $saleReturns = SaleReturn::all();
        }


        if ($id != null) {
            $sales = $sales->where("people_id", $id);

            $saleDebts = $saleDebts->where("people_id", $id);

            $saleReturns = $saleReturns->filter(function ($return) use ($id) {
                return $return->sale->people_id == $id;
            });
        }
        foreach ($sales as $sale) {
            $array[] = [
                "tableName" => "sales",
                "clientType" => $sale->people->type,
                "type" => null,
                "real" => false,
                "invoice_id" => $sale->id,
                "id" => $sale->id,
                "debit" => $sale->amount,
                "credit" => 0,
                "due_date" => $sale->due_date,
                "payment" => null,
                "bank" => null,
                "bank_id" => null,
                "note" => "فاتورة مبيعات رقم #" . $sale->id,
                "owner_id" => $sale->people_id,
                "ownerName" => $sale->people->name,
                "created_at" => $sale->created_at,
                "updated_at" => $sale->updated_at,
            ];

            if ($sale->paid != 0) {
                $array[] = [
                    "tableName" => "sales",
                    "clientType" => $sale->people->type,
                    "type" => null,
                    "real" => true,
                    "invoice_id" => $sale->id,
                    "id" => $sale->id,
                    "debit" => 0,
                    "credit" => $sale->paid,
                    "due_date" => $sale->due_date,
                    "payment" => "cash",
                    "bank" => null,
                    "bank_id" => null,
                    "note" => "مدفوعات فاتورة مبيعات رقم #" . $sale->id,
                    "owner_id" => $sale->people_id,
                    "ownerName" => $sale->people->name,
                    "created_at" => $sale->created_at,
                    "updated_at" => $sale->updated_at,
                ];
            }

            if ($sale->bank_paid != 0) {
                $array[] = [
                    "tableName" => "sales",
                    "clientType" => $sale->people->type,
                    "type" => null,
                    "real" => true,
                    "invoice_id" => $sale->id,
                    "id" => $sale->id,
                    "debit" => 0,
                    "credit" => $sale->bank_paid,
                    "due_date" => $sale->due_date,
                    "payment" => "bank",
                    "bank" => $sale->bank,
                    "bank_id" => $sale->bank_id,
                    "note" => "مدفوعات فاتورة مبيعات رقم #" . $sale->id,
                    "owner_id" => $sale->people_id,
                    "ownerName" => $sale->people->name,
                    "created_at" => $sale->created_at,
                    "updated_at" => $sale->updated_at,
                ];
            }
        }

        foreach ($saleReturns as $return) {
            $array[] = [
                "tableName" => "sale_returns",
                "clientType" => $return->sale->people->type,
                "type" => null,
                "real" => false,
                "invoice_id" => $return->sale_id,
                "id" => $return->id,
                "debit" => 0,
                "credit" => $return->price * $return->quantity,
                "due_date" => $return->due_date,
                "payment" => null,
                "bank" => $return->bank,
                "bank_id" => $return->bank_id,
                "note" => "مرتجعات فاتورة مبيعات رقم #" . $return->sale_id,
                "owner_id" => $return->sale->people_id,
                "ownerName" => $return->sale->people->name,
                "created_at" => $return->created_at,
                "updated_at" => $return->updated_at,
            ];
            if ($return->amount != 0) {
                $array[] = [
                    "tableName" => "sale_returns",
                    "clientType" => $return->sale->people->type,
                    "type" => null,
                    "real" => true,
                    "invoice_id" => $return->sale_id,
                    "id" => $return->id,
                    "debit" => $return->amount,
                    "credit" => 0,
                    "due_date" => $return->due_date,
                    "payment" => $return->payment,
                    "bank" => $return->bank,
                    "bank_id" => $return->bank_id,
                    "note" => "مدفوعات مرتجعات فاتورة مبيعات رقم #" . $return->sale_id,
                    "owner_id" => $return->sale->people_id,
                    "ownerName" => $return->sale->people->name,
                    "created_at" => $return->created_at,
                    "updated_at" => $return->updated_at,
                ];
            }
        }

        foreach ($saleDebts as $debt) {
            $array[] = [
                "tableName" => "sale_debts",
                "clientType" => $debt->people->type,
                "type" => $debt->type,
                "real" => !($debt->type == "discount"),
                "invoice_id" => null,
                "id" => $debt->id,
                "debit" => $debt->type == "debt" ? $debt->amount : 0,
                "credit" => $debt->type == "pay" || $debt->type == "discount" ? $debt->amount : 0,
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
