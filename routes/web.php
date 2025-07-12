<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/premium', [StripeController::class, 'checkout'])->name('premium.checkout');
    Route::get('/success', [StripeController::class, 'success'])->name('premium.success');
});

Route::post('/stripe/webhook', [StripeController::class, 'webhook']);
