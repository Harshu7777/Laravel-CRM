<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AbandonedCartController;

// ── Public Routes ─────────────────────────────────────────────

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('/register', [UserController::class, 'userRegister'])->name('register.submit');

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('login');
})->name('login');

Route::post('/login', [UserController::class, 'loginUser'])->name('login.submit');

// ── Password Reset ────────────────────────────────────────────

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('password.update');

// ── Stripe (public) ───────────────────────────────────────────
Route::get('/stripe', [StripeController::class, 'index'])->name('stripe.index');
Route::post('/stripe', [StripeController::class, 'store'])->name('stripe.payment');

// ── Checkout Success/Cancel (Stripe redirects — outside auth) ─
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

// ── Authenticated Routes ──────────────────────────────────────

Route::middleware('auth')->group(function () {

    // Home / Index
    Route::get('/index', function () {
        $products = \App\Models\Product::latest()->take(4)->get();
        return view('index', compact('products'));
    })->name('index');

    // Dashboard
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return view('dashboard', [
            'user' => $user,
            'role' => $user->role ?? 'customer',
            'name' => $user->name ?? 'User',
        ]);
    })->name('dashboard');

    // Profile
    Route::get('/profile', function () {
        $user = Auth::user();
        return view('profile', compact('user'));
    })->name('profile');

    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/me', [UserController::class, 'me'])->name('user.me');

    // ── 2FA ──────────────────────────────────────────────────
    Route::post('/2fa/verify', [UserController::class, 'verify2FA'])->name('2fa.verify');
    Route::post('/2fa/enable', [UserController::class, 'enable2FA'])->name('2fa.enable');
    Route::post('/2fa/toggle', [UserController::class, 'toggle2FA'])->name('2fa.toggle');

    // ── Cart ─────────────────────────────────────────────────
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::get('/cart/items', [CartController::class, 'getItems'])->name('cart.items');

    // ── Checkout ─────────────────────────────────────────────
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/create-checkout-session', [CheckoutController::class, 'createCheckoutSession'])->name('checkout.session');

    // ── Orders ───────────────────────────────────────────────
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::patch('/orders/{order}/shipping', [OrderController::class, 'updateShipping'])->name('orders.updateShipping');
    Route::get('/orders/{order}/download-invoice', [OrderController::class, 'downloadInvoice'])->name('orders.downloadInvoice');

    // ── Admin Routes ─────────────────────────────────────────
    Route::middleware('admin')->group(function () {

        Route::resource('users', UserController::class);
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);

        Route::get('/categories/data', [CategoryController::class, 'getData'])->name('categories.data');
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');

        Route::get('/abandoned-carts', [AbandonedCartController::class, 'index'])->name('admin.abandoned-carts.index');
        Route::post('/abandoned-carts/{id}/send-mail', [AbandonedCartController::class, 'sendMail'])->name('admin.abandoned-carts.send-mail');
        Route::get('/admin/leads', [AbandonedCartController::class, 'index'])->name('lead');

    });

});