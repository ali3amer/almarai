<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Safe extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getPastBalanceAttribute()
    {
        return $this->initialBalance
            + SaleDebt::where("type", "pay")->where("due_date", "<", session("date"))->where("payment", "cash")->sum("paid")
            - SaleDebt::where("type", "debt")->where("due_date", "<", session("date"))->where("payment", "cash")->whereNull("sale_id")->sum("debt")
            + Transfer::where("transfer_type", "bank_to_cash")->where("transfer_date", "<", session("date"))->sum("transfer_amount")
            - Transfer::where("transfer_type", "cash_to_bank")->where("transfer_date", "<", session("date"))->sum("transfer_amount")
            - Expense::where("payment", "cash")->where("expense_date", "<", session("date"))->sum("amount")
            - EmployeeGift::where("payment", "cash")->where("gift_date", "<", session("date"))->sum("gift_amount")
            - PurchaseDebt::where("type", "pay")->where("due_date", "<", session("date"))->where("payment", "cash")->sum("paid")
            + PurchaseDebt::where("type", "debt")->where("due_date", "<", session("date"))->where("payment", "cash")->whereNull("purchase_id")->sum("debt")
            - Withdraw::where("due_date", session("date"))->sum("amount");
    }
    public function getcurrentBalanceAttribute()
    {
        return $this->initialBalance
            + Withdraw::sum("amount")
            + SaleDebt::where("type", "pay")->where("payment", "cash")->sum("paid")
            - SaleDebt::where("type", "debt")->where("payment", "cash")->whereNull("sale_id")->sum("debt")
            + Transfer::where("transfer_type", "bank_to_cash")->sum("transfer_amount")
            - Transfer::where("transfer_type", "cash_to_bank")->sum("transfer_amount")
            - Expense::where("payment", "cash")->sum("amount")
            - EmployeeGift::where("payment", "cash")->sum("gift_amount")
            - PurchaseDebt::where("type", "pay")->where("payment", "cash")->sum("paid")
            + PurchaseDebt::where("type", "debt")->where("payment", "cash")->whereNull("purchase_id")->sum("debt");
    }
    public function getSafeDayBalanceAttribute()
    {
        $safe = $this->startingDate == session("date") ? $this->initialBalance : 0;
        return $safe
            + Withdraw::where("due_date", session("date"))->sum("amount")
            + SaleDebt::where("type", "pay")->where("due_date", session("date"))->where("payment", "cash")->sum("paid")
            - SaleDebt::where("type", "debt")->where("due_date", session("date"))->where("payment", "cash")->whereNull("sale_id")->sum("debt")
            + Transfer::where("transfer_type", "bank_to_cash")->where("transfer_date", session("date"))->sum("transfer_amount")
            - Transfer::where("transfer_type", "cash_to_bank")->where("transfer_date", session("date"))->sum("transfer_amount")
            - Expense::where("payment", "cash")->where("expense_date", session("date"))->sum("amount")
            - EmployeeGift::where("payment", "cash")->where("gift_date", session("date"))->sum("gift_amount")
            - PurchaseDebt::where("type", "pay")->where("payment", "cash")->where("due_date", session("date"))->sum("paid")
            + PurchaseDebt::where("type", "debt")->where("payment", "cash")->where("due_date", session("date"))->whereNull("purchase_id")->sum("debt");

    }
}
