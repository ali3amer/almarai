<?php

namespace App\Http\Controllers;

use App\Models\Day;
use Illuminate\Http\Request;
use function Laravel\Prompts\search;

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
        $day = Day::where("due_date", session("date"))->first();

        if (!$day->closed) {
            $day->closed = true;
            $day->balance = session("safeBalance");
            $day->save();
        }
        session(["closed" => true]);
        return back();
    }
}
