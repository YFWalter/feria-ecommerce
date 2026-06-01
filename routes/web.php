<?php

use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

// ── Tienda pública ──────────────────────────────────────────
Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('/productos', [ShopController::class, 'index'])->name('products.index');
Route::get('/productos/{slug}', [ShopController::class, 'show'])->name('products.show');

// Carrito
Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrito/agregar', [CartController::class, 'add'])->name('cart.add');
Route::patch('/carrito/{rowId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrito/{rowId}', [CartController::class, 'remove'])->name('cart.remove');

// Checkout y pedido
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/pendiente', [CheckoutController::class, 'pending'])->name('checkout.pending');
Route::get('/checkout/fallo', [CheckoutController::class, 'failure'])->name('checkout.failure');
Route::get('/pedido/{number}', [CheckoutController::class, 'success'])->name('checkout.success');

// Webhook (IPN) de MercadoPago — sin CSRF (ver bootstrap/app.php)
Route::post('/webhooks/mercadopago', [CheckoutController::class, 'webhook'])->name('checkout.webhook');

// ── Redirección post-login (Breeze apunta a route('dashboard')) ──
// Admin → panel; cliente → home.
Route::get('/dashboard', function () {
    return auth()->user()?->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('home');
})->middleware('auth')->name('dashboard');

// ── Panel de administración ─────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('productos', ProductController::class)
        ->parameters(['productos' => 'product'])
        ->except(['show']);
    Route::resource('categorias', CategoryController::class)
        ->parameters(['categorias' => 'category'])
        ->except(['show']);
    Route::resource('pedidos', OrderController::class)
        ->parameters(['pedidos' => 'order'])
        ->only(['index', 'show', 'update']);
});

// ── Auth (generado por Breeze) ──────────────────────────────
require __DIR__ . '/auth.php';
