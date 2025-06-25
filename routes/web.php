<?php

use App\Http\Controllers\Zoho\ZohoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('zoho-form', function () {
    return Inertia::render('zoho/ZohoForm');
})->name('zoho-form');

Route::post('/zoho/create-deal-account', [ZohoController::class, 'createDealAccount'])->name('zoho.create-deal-account');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
