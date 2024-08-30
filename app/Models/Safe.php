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
            + Sale::where("payment", "cash")->where("due_date", "<", session("date"))->sum("paid")
            - Purchase::where("payment", "cash")->where("due_date", "<", session("date"))->sum("paid")
            + SaleDebt::where("type", "pay")->where("due_date", "<", session("date"))->where("payment", "cash")->sum("amount")
            - SaleDebt::where("type", "debt")->where("due_date", "<", session("date"))->where("payment", "cash")->sum("amount")
            + DepositDebt::where("type", "pay")->where("due_date", "<", session("date"))->where("payment", "cash")->sum("amount")
            - DepositDebt::where("type", "debt")->where("due_date", "<", session("date"))->where("payment", "cash")->sum("amount")
            + Transfer::where("transfer_type", "bank_to_cash")->where("due_date", "<", session("date"))->sum("amount")
            - Transfer::where("transfer_type", "cash_to_bank")->where("due_date", "<", session("date"))->sum("amount")
            - Expense::where("payment", "cash")->where("due_date", "<", session("date"))->sum("amount")
            - EmployeeGift::where("payment", "cash")->where("due_date", "<", session("date"))->sum("amount")
            - PurchaseDebt::where("type", "pay")->where("due_date", "<", session("date"))->where("payment", "cash")->sum("amount")
            + PurchaseDebt::where("type", "debt")->where("due_date", "<", session("date"))->where("payment", "cash")->sum("amount")
            - Withdraw::where("due_date", session("date"))->sum("amount")
            - SaleReturn::where("due_date", "<", session("date"))->sum("amount")
            + PurchaseReturn::where("due_date", "<", session("date"))->sum("amount");
    }

    public function getCurrentBalanceAttribute()
    {
        return $this->initialBalance
            + Sale::where("payment", "cash")->sum("paid")
            - Purchase::where("payment", "cash")->sum("paid")
            + SaleDebt::where("type", "pay")->where("payment", "cash")->sum("amount")
            - SaleDebt::where("type", "debt")->where("payment", "cash")->sum("amount")
            + DepositDebt::where("type", "pay")->where("payment", "cash")->sum("amount")
            - DepositDebt::where("type", "debt")->where("payment", "cash")->sum("amount")
            + Transfer::where("transfer_type", "bank_to_cash")->sum("amount")
            - Transfer::where("transfer_type", "cash_to_bank")->sum("amount")
            - Expense::where("payment", "cash")->sum("amount")
            - EmployeeGift::where("payment", "cash")->sum("amount")
            - PurchaseDebt::where("type", "pay")->where("payment", "cash")->sum("amount")
            + PurchaseDebt::where("type", "debt")->where("payment", "cash")->sum("amount")
            - SaleReturn::sum("amount")
            + PurchaseReturn::sum("amount");
    }

    public function getSafeDayBalanceAttribute()
    {
        $safe = $this->startingDate == session("date") ? $this->initialBalance : 0;
        return $safe
            + Withdraw::where("due_date", session("date"))->sum("amount")
            + Sale::where("payment", "cash")->where("due_date", session("date"))->sum("paid")
            - Purchase::where("payment", "cash")->where("due_date", session("date"))->sum("paid")
            + SaleDebt::where("type", "pay")->where("due_date", session("date"))->where("payment", "cash")->where("type", "pay")->sum("amount")
            - SaleDebt::where("type", "debt")->where("due_date", session("date"))->where("payment", "cash")->where("type", "debt")->sum("amount")
            + DepositDebt::where("type", "pay")->where("due_date", session("date"))->where("payment", "cash")->sum("amount")
            - DepositDebt::where("type", "debt")->where("due_date", session("date"))->where("payment", "cash")->sum("amount")
            + Transfer::where("transfer_type", "bank_to_cash")->where("due_date", session("date"))->sum("amount")
            - Transfer::where("transfer_type", "cash_to_bank")->where("due_date", session("date"))->sum("amount")
            - Expense::where("payment", "cash")->where("due_date", session("date"))->sum("amount")
            - EmployeeGift::where("payment", "cash")->where("due_date", session("date"))->sum("amount")
            - PurchaseDebt::where("type", "pay")->where("payment", "cash")->where("due_date", session("date"))->where("type", "pay")->sum("amount")
            + PurchaseDebt::where("type", "debt")->where("payment", "cash")->where("due_date", session("date"))->where("type", "debt")->sum("amount")
            - SaleReturn::where("due_date", session("date"))->sum("amount")
            + PurchaseReturn::where("due_date", session("date"))->sum("amount");
    }

    public function getSafeDayBalance($date)
    {
        $safe = $this->startingDate == $date ? $this->initialBalance : 0;
        return $safe
            + Withdraw::where("due_date", $date)->sum("amount")
            + Sale::where("payment", "cash")->where("due_date", $date)->sum("paid")
            - Purchase::where("payment", "cash")->where("due_date", $date)->sum("paid")
            + SaleDebt::where("type", "pay")->where("due_date", $date)->where("payment", "cash")->where("type", "pay")->sum("amount")
            - SaleDebt::where("type", "debt")->where("due_date", $date)->where("payment", "cash")->where("type", "debt")->sum("amount")
            + DepositDebt::where("type", "pay")->where("due_date", $date)->where("payment", "cash")->sum("amount")
            - DepositDebt::where("type", "debt")->where("due_date", $date)->where("payment", "cash")->sum("amount")
            + Transfer::where("transfer_type", "bank_to_cash")->where("due_date", $date)->sum("amount")
            - Transfer::where("transfer_type", "cash_to_bank")->where("due_date", $date)->sum("amount")
            - Expense::where("payment", "cash")->where("due_date", $date)->sum("amount")
            - EmployeeGift::where("payment", "cash")->where("due_date", $date)->sum("amount")
            - PurchaseDebt::where("type", "pay")->where("payment", "cash")->where("due_date", $date)->where("type", "pay")->sum("amount")
            + PurchaseDebt::where("type", "debt")->where("payment", "cash")->where("due_date", $date)->where("type", "debt")->sum("amount")
            - SaleReturn::where("due_date", $date)->sum("amount")
            + PurchaseReturn::where("due_date", $date)->sum("amount");
    }

    public function getSafeBetweenBalance($from, $to)
    {
        $safe = $this->startingDate == $from ? $this->initialBalance : 0;
        return $safe
            + Withdraw::whereBetween("due_date", [$from, $to])->sum("amount")
            + Sale::where("payment", "cash")->whereBetween("due_date", [$from, $to])->sum("paid")
            - Purchase::where("payment", "cash")->whereBetween("due_date", [$from, $to])->sum("paid")
            + SaleDebt::where("type", "pay")->whereBetween("due_date", [$from, $to])->where("payment", "cash")->where("type", "pay")->sum("amount")
            - SaleDebt::where("type", "debt")->whereBetween("due_date", [$from, $to])->where("payment", "cash")->where("type", "debt")->sum("amount")
            + DepositDebt::where("type", "pay")->whereBetween("due_date", [$from, $to])->where("payment", "cash")->sum("amount")
            - DepositDebt::where("type", "debt")->whereBetween("due_date", [$from, $to])->where("payment", "cash")->sum("amount")
            + Transfer::where("transfer_type", "bank_to_cash")->whereBetween("due_date", [$from, $to])->sum("amount")
            - Transfer::where("transfer_type", "cash_to_bank")->whereBetween("due_date", [$from, $to])->sum("amount")
            - Expense::where("payment", "cash")->whereBetween("due_date", [$from, $to])->sum("amount")
            - EmployeeGift::where("payment", "cash")->whereBetween("due_date", [$from, $to])->sum("amount")
            - PurchaseDebt::where("type", "pay")->where("payment", "cash")->whereBetween("due_date", [$from, $to])->where("type", "pay")->sum("amount")
            + PurchaseDebt::where("type", "debt")->where("payment", "cash")->whereBetween("due_date", [$from, $to])->where("type", "debt")->sum("amount")
            - SaleReturn::whereBetween("due_date", [$from, $to])->sum("amount")
            + PurchaseReturn::whereBetween("due_date", [$from, $to])->sum("amount");
    }
}
