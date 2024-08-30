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

    public function getDayBalance($date)
    {
        $initial = Bank::where("startingDate", $date)->sum("initialBalance");
        return $initial
            + Sale::where("payment", "bank")->where("due_date", $date)->sum("paid")
            - Purchase::where("payment", "bank")->where("due_date", $date)->sum("paid")
            + SaleDebt::where("type", "pay")->where("due_date", $date)->where("payment", "bank")->sum("amount")
            - SaleDebt::where("type", "debt")->where("due_date", $date)->where("payment", "bank")->sum("amount")
            + DepositDebt::where("type", "pay")->where("due_date", $date)->where("payment", "bank")->sum("amount")
            - DepositDebt::where("type", "debt")->where("due_date", $date)->where("payment", "bank")->sum("amount")
            + Transfer::where("transfer_type", "cash_to_bank")->where("due_date", $date)->sum("amount")
            - Transfer::where("transfer_type", "bank_to_cash")->where("due_date", $date)->sum("amount")
            - Expense::where("payment", "bank")->where("due_date", $date)->sum("amount")
            - EmployeeGift::where("payment", "bank")->where("due_date", $date)->sum("amount")
            - PurchaseDebt::where("type", "pay")->where("payment", "bank")->where("due_date", $date)->sum("amount")
            + PurchaseDebt::where("type", "debt")->where("payment", "bank")->where("due_date", $date)->sum("amount");
    }

    public function getBetweenBalance($from, $to)
    {
        $initial = Bank::whereBetween("startingDate", [$from, $to])->sum("initialBalance");
        return $initial
            + Sale::where("payment", "bank")->whereBetween("due_date", [$from, $to])->sum("paid")
            - Purchase::where("payment", "bank")->whereBetween("due_date", [$from, $to])->sum("paid")
            + SaleDebt::where("type", "pay")->whereBetween("due_date", [$from, $to])->where("payment", "bank")->sum("amount")
            - SaleDebt::where("type", "debt")->whereBetween("due_date", [$from, $to])->where("payment", "bank")->sum("amount")
            + DepositDebt::where("type", "pay")->whereBetween("due_date", [$from, $to])->where("payment", "bank")->sum("amount")
            - DepositDebt::where("type", "debt")->whereBetween("due_date", [$from, $to])->where("payment", "bank")->sum("amount")
            + Transfer::where("transfer_type", "cash_to_bank")->whereBetween("due_date", [$from, $to])->sum("amount")
            - Transfer::where("transfer_type", "bank_to_cash")->whereBetween("due_date", [$from, $to])->sum("amount")
            - Expense::where("payment", "bank")->whereBetween("due_date", [$from, $to])->sum("amount")
            - EmployeeGift::where("payment", "bank")->whereBetween("due_date", [$from, $to])->sum("amount")
            - PurchaseDebt::where("type", "pay")->where("payment", "bank")->whereBetween("due_date", [$from, $to])->sum("amount")
            + PurchaseDebt::where("type", "debt")->where("payment", "bank")->whereBetween("due_date", [$from, $to])->sum("amount");
    }

    public function getCurrentTotalBalance()
    {
        return Bank::sum("initialBalance")
            + Sale::where("payment", "bank")->sum("paid")
            - Purchase::where("payment", "bank")->sum("paid")
            + SaleDebt::where("type", "pay")->where("payment", "bank")->sum("amount")
            - SaleDebt::where("type", "debt")->where("payment", "bank")->sum("amount")
            + DepositDebt::where("type", "pay")->where("payment", "bank")->sum("amount")
            - DepositDebt::where("type", "debt")->where("payment", "bank")->sum("amount")
            + Transfer::where("transfer_type", "cash_to_bank")->sum("amount")
            - Transfer::where("transfer_type", "bank_to_cash")->sum("amount")
            - Expense::where("payment", "bank")->sum("amount")
            - EmployeeGift::where("payment", "bank")->sum("amount")
            - PurchaseDebt::where("type", "pay")->where("payment", "bank")->sum("amount")
            + PurchaseDebt::where("type", "debt")->where("payment", "bank")->sum("amount");
    }
}
