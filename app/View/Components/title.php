<?php

namespace App\View\Components;

use App\Models\Bank;
use App\Models\Day;
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
use function Symfony\Component\String\b;

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
        $safeBalance = Safe::first()->safeDayBalance;

        $banks = Bank::all();
        $bankBalance = 0;
        foreach ($banks as $bank) {
            $bankBalance += $bank->currentBalance;
        }

        $count = Day::where("due_date", session('date'))->count();

        if ($count != 0) {
            $closed = Day::where("due_date", session('date'))->first()->closed;
        } else {
            Day::create([
                "due_date" => session("date"),
                "closed" => false,
                "balance" => $safeBalance,
                "user_id" => auth()->id()
            ]);
            $closed = false;
        }

        session(["closed" => $closed]);
        session(['safeBalance' => $safeBalance]);
        session(['bankBalance' => $bankBalance]);
        return view('components.title', [
            'safeBalance' => $safeBalance,
            'bankBalance' => $bankBalance
        ]);

    }
}
