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
            + Sale::where("bank_id", $this->id)->where("payment", "bank")->sum("paid")
            - Purchase::where("bank_id", $this->id)->where("payment", "bank")->sum("paid")
            + SaleDebt::where("bank_id", $this->id)->where("type", "pay")->where("payment", "bank")->sum("amount")
            - SaleDebt::where("bank_id", $this->id)->where("type", "debt")->where("payment", "bank")->sum("amount")
            + DepositDebt::where("bank_id", $this->id)->where("type", "pay")->where("payment", "bank")->sum("amount")
            - DepositDebt::where("bank_id", $this->id)->where("type", "debt")->where("payment", "bank")->sum("amount")
            + Transfer::where("bank_id", $this->id)->where("transfer_type", "cash_to_bank")->sum("amount")
            - Transfer::where("bank_id", $this->id)->where("transfer_type", "bank_to_cash")->sum("amount")
            - Expense::where("bank_id", $this->id)->where("payment", "bank")->sum("amount")
            - EmployeeGift::where("bank_id", $this->id)->where("payment", "bank")->sum("amount")
            - PurchaseDebt::where("bank_id", $this->id)->where("type", "pay")->where("payment", "bank")->sum("amount")
            + PurchaseDebt::where("bank_id", $this->id)->where("type", "debt")->where("payment", "bank")->sum("amount");
    }

    public function getCurrentTotalBalance()
    {
        return $this->initialBalance
            + Sale::where("payment", "bank")->sum("paid")
            - Purchase::where("payment", "bank")->sum("paid")
            + SaleDebt::where("type", "pay")->where("payment", "bank")->sum("amount")
            - SaleDebt::where("type", "debt")->where("payment", "bank")->sum("amount")
            + DepositDebt::where("type", "pay")->where("payment", "bank")->sum("amount")
            - DepositDebt::where("type", "debt")->where("payment", "bank")->sum("amount")
            + Transfer::where("transfer_type", "bank_to_cash")->sum("amount")
            - Transfer::where("transfer_type", "cash_to_bank")->sum("amount")
            - Expense::where("payment", "bank")->sum("amount")
            - EmployeeGift::where("payment", "bank")->sum("amount")
            - PurchaseDebt::where("type", "pay")->where("payment", "bank")->sum("amount")
            + PurchaseDebt::where("type", "debt")->where("payment", "bank")->sum("amount");
    }
}
