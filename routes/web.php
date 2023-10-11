<?php

use App\Livewire\Category;
use App\Livewire\Client;
use App\Livewire\Counter;
use App\Livewire\Employee;
use App\Livewire\Expense;
use App\Livewire\Purchase;
use App\Livewire\Sale;
use App\Livewire\Store;
use App\Livewire\Supplier;
use App\Livewire\Product;
use Illuminate\Support\Facades\Route;

use App\Livewire;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::get('/counter', Counter::class);
    Route::get('/store', Store::class);
    Route::get('/category', Category::class);
    Route::get('/product', Product::class);
    Route::get('/supplier', Supplier::class);
    Route::get('/client', Client::class);
    Route::get('/purchase', Purchase::class);
    Route::get('/sale', Sale::class);
    Route::get('/expense', Expense::class);
    Route::get('/employee', Employee::class);
    Route::get('/report', Livewire\Report::class);
    Route::get('/returns', Livewire\Returns::class);
    Route::get('/purchase-returns', Livewire\PurchaseReturns::class);
    Route::get('/safe', Livewire\Safe::class);
    Route::get('/damaged', Livewire\Damaged::class);
    Route::get('/debt', Livewire\Debt::class);
    Route::get('/claim', Livewire\Claim::class);
    Route::get('/user', Livewire\User::class);
});

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
