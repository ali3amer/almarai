<?php

namespace App\Http\Controllers;

use App\Models\Day;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function changeDate(Request $request)
    {
        session(["date" => $request->date]);
        return back();

    }
    public function closeDay(Request $request)
    {
        $count = Day::where("due_date", session("date"))->count();

        if ($count == 0) {
            Day::create([
                "due_date" => session("date"),
                "closed" => true,
                "balance" => session("safeBalance"),
                'user_id' => auth()->id()
        ]);
        }
        session(["closed" => true]);
        return back();
    }
}
