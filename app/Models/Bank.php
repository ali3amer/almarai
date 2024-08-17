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
        return $this->initialBalance
            + Sale::where("bank_id", $this->id)->where("payment", "bank")->sum("amount")
            - Purchase::where("bank_id", $this->id)->where("payment", "bank")->sum("amount")
            + SaleDebt::where("type", "pay")->where("bank_id", $this->id)->where("payment", "bank")->sum("amount")
            - SaleDebt::where("type", "debt")->where("bank_id", $this->id)->where("payment", "bank")->sum("amount")
            + DepositDebt::where("type", "pay")->where("due_date", session("date"))->where("payment", "bank")->sum("amount")
            - DepositDebt::where("type", "debt")->where("due_date", session("date"))->where("payment", "bank")->sum("amount")
            + Transfer::where("transfer_type", "cash_to_bank")->where("bank_id", $this->id)->sum("amount")
            - Transfer::where("transfer_type", "bank_to_cash")->where("bank_id", $this->id)->sum("amount")
            - Expense::where("payment", "bank")->where("bank_id", $this->id)->sum("amount")
            - EmployeeGift::where("payment", "bank")->where("bank_id", $this->id)->sum("amount")
            - PurchaseDebt::where("type", "pay")->where("payment", "bank")->where("bank_id", $this->id)->sum("amount")
            + PurchaseDebt::where("type", "debt")->where("payment", "bank")->where("bank_id", $this->id)->sum("amount");
    }
}
