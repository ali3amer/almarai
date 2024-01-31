<?php

namespace App\View\Components;

use App\Models\Bank;
use App\Models\EmployeeGift;
use App\Models\Expense;
use App\Models\PurchaseDebt;
use App\Models\Safe;
use App\Models\SaleDebt;
use App\Models\Transfer;
use App\Models\Withdraw;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class title extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public string $title)
    {
        if (session("date") == null) {
            session(['date' => date("Y-m-d")]);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        // real title
        $date = session("date") ?? session(['date' => date("Y-m-d")]);
        $safe = Safe::where("startingDate", $date)->first()->initialBalance ?? 0;
        $safeBalance = $safe
            + Withdraw::where("due_date", $date)->sum("amount")
            + SaleDebt::where("type", "pay")->where("due_date", $date)->where("payment", "cash")->sum("paid")
            - SaleDebt::where("type", "debt")->where("due_date", $date)->where("payment", "cash")->whereNull("sale_id")->sum("debt")
            + Transfer::where("transfer_type", "bank_to_cash")->where("transfer_date", $date)->sum("transfer_amount")
            - Transfer::where("transfer_type", "cash_to_bank")->where("transfer_date", $date)->sum("transfer_amount")
            - Expense::where("payment", "cash")->where("expense_date", $date)->sum("amount")
            - EmployeeGift::where("payment", "cash")->where("gift_date", $date)->sum("gift_amount")
            - PurchaseDebt::where("type", "pay")->where("payment", "cash")->where("due_date", $date)->sum("paid")
            + PurchaseDebt::where("type", "debt")->where("payment", "cash")->where("due_date", $date)->whereNull("purchase_id")->sum("debt");

        $bankBalance = Bank::sum('initialBalance')
            + SaleDebt::where("type", "pay")->where("payment", "bank")->sum("paid")
            - SaleDebt::where("type", "debt")->where("payment", "bank")->whereNull("sale_id")->sum("debt")
            + Transfer::where("transfer_type", "cash_to_bank")->sum("transfer_amount")
            - Transfer::where("transfer_type", "bank_to_cash")->sum("transfer_amount")
            - Expense::where("payment", "bank")->sum("amount")
            - EmployeeGift::where("payment", "bank")->sum("gift_amount")
            - PurchaseDebt::where("type", "pay")->where("payment", "bank")->sum("paid")
            + PurchaseDebt::where("type", "debt")->where("payment", "bank")->whereNull("purchase_id")->sum("debt");

        session(['safeBalance' => $safeBalance]);
        session(['bankBalance' => $bankBalance]);
        return view('components.title', [
            'safeBalance' => $safeBalance,
            'bankBalance' => $bankBalance
        ]);

    }
}
