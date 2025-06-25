<?php

use App\Http\Controllers\Zoho\ZohoController;
use Illuminate\Support\Facades\Route;

Route::get('/zoho/deal-stages', [ZohoController::class, 'getDealStages']);
