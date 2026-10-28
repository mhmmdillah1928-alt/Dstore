<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Seller;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

// Publik
Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/produk/{product:slug}', [ShopController::class, 'show'])->name('product.show');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Customer
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/keranjang/item/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/item/{item}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/pesanan/{order}/pembayaran', [OrderController::class, 'payment'])->name('orders.payment');
    Route::post('/pesanan/{order}/bukti-pembayaran', [OrderController::class, 'submitPaymentProof'])->name('orders.payment-proof.store');
    Route::post('/pesanan/{order}/batal', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/pesanan/{order}/selesai', [OrderController::class, 'complete'])->name('orders.complete');
    Route::post('/pesanan/{order}/sengketa', [OrderController::class, 'dispute'])->name('orders.dispute');
    Route::post('/ulasan/{item}', [OrderController::class, 'review'])->name('orders.review');
});

// Seller
Route::prefix('seller')->name('seller.')->middleware(['auth', 'role:seller'])->group(function () {
    Route::get('/', [Seller\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/toko', [Seller\DashboardController::class, 'editStore'])->name('store.edit');
    Route::put('/toko', [Seller\DashboardController::class, 'updateStore'])->name('store.update');

    Route::resource('products', Seller\ProductController::class)->except('show');
    Route::delete('products/image/{image}', [Seller\ProductController::class, 'destroyImage'])->name('products.image.destroy');

    Route::get('/pesanan', [Seller\OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [Seller\OrderController::class, 'show'])->name('orders.show');
    Route::get('/pesanan/{order}/bukti-pembayaran', [Seller\OrderController::class, 'paymentProof'])->name('orders.payment-proof');
    Route::post('/pesanan/{order}/pembayaran/setujui', [Seller\OrderController::class, 'approvePayment'])->name('orders.payment.approve');
    Route::post('/pesanan/{order}/pembayaran/tolak', [Seller\OrderController::class, 'rejectPayment'])->name('orders.payment.reject');
    Route::post('/pesanan/{order}/konfirmasi', [Seller\OrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('/pesanan/{order}/kirim', [Seller\OrderController::class, 'ship'])->name('orders.ship');

    Route::get('/saldo', [Seller\WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/saldo', [Seller\WithdrawalController::class, 'store'])->name('withdrawals.store');
});

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/toko', [AdminController::class, 'stores'])->name('stores');
    Route::post('/toko/{store}/verifikasi', [AdminController::class, 'toggleStore'])->name('stores.toggle');
    Route::get('/produk', [AdminController::class, 'products'])->name('products');
    Route::post('/produk/{product}/toggle', [AdminController::class, 'toggleProduct'])->name('products.toggle');
    Route::get('/pengaturan', [AdminController::class, 'settings'])->name('settings');
    Route::put('/pengaturan', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::get('/penarikan', [AdminController::class, 'withdrawals'])->name('withdrawals');
    Route::post('/penarikan/{withdrawal}', [AdminController::class, 'resolveWithdrawal'])->name('withdrawals.resolve');
    Route::get('/sengketa', [AdminController::class, 'disputes'])->name('disputes.index');
    Route::get('/sengketa/{dispute}', [AdminController::class, 'showDispute'])->name('disputes.show');
    Route::post('/sengketa/{dispute}', [AdminController::class, 'resolveDispute'])->name('disputes.resolve');
});
