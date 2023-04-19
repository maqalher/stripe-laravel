<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/plans', [SubscriptionController::class, 'allPlans'])->name('plans.all');
Route::post('/single-charge', [App\Http\Controllers\HomeController::class, 'singleCharge'])->name('single.charge');
Route::get('/plans/create', [SubscriptionController::class, 'showPlanForm'])->name('plans.create');
Route::post('/plans/store', [SubscriptionController::class, 'savePlan'])->name('plans.store');

Route::get('/plans/checkout/{planId}', [SubscriptionController::class, 'checkout'])->name('plans.checkout');

Route::post('/plans/process', [SubscriptionController::class, 'processPlan'])->name('plans.process');

Route::get('/subscription/all', [SubscriptionController::class, 'allSubscription'])->name('subscription.all');
Route::get('/subscription/cancel', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
Route::get('/subscription/resume', [SubscriptionController::class, 'resumeSubscription'])->name('subscription.resume');

Route::get('/pl', [SubscriptionController::class, 'pl'])->name('plans.pl');

Route::get('/link', [SubscriptionController::class, 'link'])->name('plans.link');

// Route::get('/plans', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
