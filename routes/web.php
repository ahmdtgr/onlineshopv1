<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\StoreShow;
use App\Livewire\ProductDetail;
use App\Livewire\ShoppingCart;
use App\Livewire\OrderPage;
use App\Livewire\OrderDetail;
use App\Livewire\Checkout;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\PaymentConfirmationPage;
use App\Livewire\Profile;
use App\Livewire\ChangelogPage;
use App\Http\Controllers\InvoiceController;

Route::get('/', StoreShow::class)->name('home');
Route::get('/product/{slug}', ProductDetail::class)->name('product.detail');
Route::get('/changelog', ChangelogPage::class)->name('changelog');


Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)->name('register');
    Route::get('forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/shopping-cart', ShoppingCart::class)->name('shopping-cart');
    Route::get('/orders', OrderPage::class)->name('orders');

    Route::get('/order-detail/{orderNumber}', OrderDetail::class)->name('order-detail');
    Route::get('/payment-confirmation/{orderNumber}', PaymentConfirmationPage::class)->name('payment-confirmation');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout', Checkout::class)->name('checkout');
});

Route::middleware(['auth', 'admin'])->group(function () {
    // Invoice routes with explicit naming
    Route::get('invoice/{order}/download', [InvoiceController::class, 'download'])->name('invoice.download');
    Route::get('invoice/{order}/preview', [InvoiceController::class, 'preview'])->name('invoice.preview');
});




require __DIR__ . '/auth.php';


