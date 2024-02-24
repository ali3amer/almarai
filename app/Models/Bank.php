<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function getCurrentBalanceAttribute()
    {
        return $this->initialBalance + SaleDebt::where("type", "pay")->where("bank_id", $this->id)->where("payment", "bank")->sum("paid")
        + Transfer::where("transfer_type", "cash_to_bank")->where("bank_id", $this->id)->sum("transfer_amount")
        - Transfer::where("transfer_type", "bank_to_cash")->where("bank_id", $this->id)->sum("transfer_amount")
        - Expense::where("payment", "bank")->where("bank_id", $this->id)->sum("amount")
        - EmployeeGift::where("payment", "bank")->where("bank_id", $this->id)->sum("gift_amount")
        - PurchaseDebt::where("type", "pay")->where("payment", "bank")->where("bank_id", $this->id)->sum("paid")
        + PurchaseDebt::where("type", "debt")->where("payment", "bank")->where("bank_id", $this->id)->whereNull("purchase_id")->sum("debt");
    }
}
