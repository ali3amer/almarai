<?php

use App\Livewire\Category;
use App\Livewire\Client;
use App\Livewire\Counter;
use App\Livewire\Purchase;
use App\Livewire\Sale;
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
    return view('welcome');
});

Route::get('/counter', Counter::class);
Route::get('/category', Category::class);
Route::get('/product', Product::class);
Route::get('/supplier', Supplier::class);
Route::get('/client', Client::class);
Route::get('/purchase', Purchase::class);
Route::get('/sale', Sale::class);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
