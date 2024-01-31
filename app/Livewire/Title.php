<?php

namespace App\Livewire;

use App\Models\Bank;
use App\Models\EmployeeGift;
use App\Models\Expense;
use App\Models\PurchaseDebt;
use App\Models\Safe;
use App\Models\SaleDebt;
use App\Models\Transfer;
use Livewire\Component;

class Title extends Component
{
    public $title;
    public $date;


    public function render()
    {
        session(["date" => $this->date]);
        if ($this->date == null) {
            $safeBalance = Safe::first()->initialBalance
                + SaleDebt::where("type", "pay")->where("payment", "cash")->sum("paid")
                - SaleDebt::where("type", "debt")->where("payment", "cash")->whereNull("sale_id")->sum("paid")
                + Transfer::where("transfer_type", "bank_to_cash")->sum("transfer_amount")
                - Transfer::where("transfer_type", "cash_to_bank")->sum("transfer_amount")
                - Expense::where("payment", "cash")->sum("amount")
                - EmployeeGift::where("payment", "cash")->sum("gift_amount")
                - PurchaseDebt::where("type", "pay")->where("payment", "cash")->sum("paid")
                + PurchaseDebt::where("type", "debt")->where("payment", "cash")->whereNull("purchase_id")->sum("debt");

            $bankBalance = Bank::sum('initialBalance')
                + SaleDebt::where("type", "pay")->where("payment", "bank")->sum("paid")
                - SaleDebt::where("type", "debt")->where("payment", "bank")->whereNull("sale_id")->sum("paid")
                + Transfer::where("transfer_type", "cash_to_bank")->sum("transfer_amount")
                - Transfer::where("transfer_type", "bank_to_cash")->sum("transfer_amount")
                - Expense::where("payment", "bank")->sum("amount")
                - EmployeeGift::where("payment", "bank")->sum("gift_amount")
                - PurchaseDebt::where("type", "pay")->where("payment", "bank")->sum("paid")
                + PurchaseDebt::where("type", "debt")->where("payment", "bank")->whereNull("purchase_id")->sum("debt");

        } else {
            $safeBalance = Safe::first()->initialBalance
                + SaleDebt::where("type", "pay")->where("due_date", $this->date)->where("payment", "cash")->sum("paid")
                - SaleDebt::where("type", "debt")->where("due_date", $this->date)->where("payment", "cash")->whereNull("sale_id")->sum("paid")
                + Transfer::where("transfer_type", "bank_to_cash")->where("transfer_date", $this->date)->sum("transfer_amount")
                - Transfer::where("transfer_type", "cash_to_bank")->where("transfer_date", $this->date)->sum("transfer_amount")
                - Expense::where("payment", "cash")->where("expense_date", $this->date)->sum("amount")
                - EmployeeGift::where("payment", "cash")->where("gift_date", $this->date)->sum("gift_amount")
                - PurchaseDebt::where("type", "pay")->where("payment", "cash")->where("due_date", $this->date)->sum("paid")
                + PurchaseDebt::where("due_date", $this->date)->where("type", "debt")->where("payment", "cash")->whereNull("purchase_id")->sum("debt");

            $bankBalance = Bank::sum('initialBalance')
                + SaleDebt::where("type", "pay")->where("payment", "bank")->sum("paid")
                - SaleDebt::where("type", "debt")->where("payment", "bank")->whereNull("sale_id")->sum("paid")
                + Transfer::where("transfer_type", "cash_to_bank")->sum("transfer_amount")
                - Transfer::where("transfer_type", "bank_to_cash")->sum("transfer_amount")
                - Expense::where("payment", "bank")->sum("amount")
                - EmployeeGift::where("payment", "bank")->sum("gift_amount")
                - PurchaseDebt::where("type", "pay")->where("payment", "bank")->sum("paid")
                + PurchaseDebt::where("type", "debt")->where("payment", "bank")->whereNull("purchase_id")->sum("debt");

        }

        session(['safeBalance' => $safeBalance]);
        session(['bankBalance' => $bankBalance]);
        return view('livewire.title', [
            'safeBalance' => $safeBalance,
            'bankBalance' => $bankBalance
        ]);
    }
}
