<?php

use App\Http\Controllers\MidtransNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Midtrans notification webhook (tidak perlu auth karena dipanggil dari Midtrans server)
Route::post('/midtrans/notification', [MidtransNotificationController::class, 'handle']);
