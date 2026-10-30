<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Customer Storefront (Light Theme Contemporary Brand)
Route::get('/', [ShopController::class, 'home'])->name('shop.home');
Route::get('/shop', [ShopController::class, 'catalog'])->name('shop.catalog');
Route::get('/collections', [ShopController::class, 'collections'])->name('shop.collections');
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('shop.product');

// AI Personal Stylist & Storefront Chatbot
Route::post('/chat/message', [ChatbotController::class, 'sendMessage'])->name('chat.message');

// Shopping Bag
Route::get('/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::post('/cart/add', [ShopController::class, 'addToCart'])->name('shop.cart.add');
Route::post('/cart/remove', [ShopController::class, 'removeFromCart'])->name('shop.cart.remove');
Route::post('/cart/clear', [ShopController::class, 'clearCart'])->name('shop.cart.clear');

// Protected Admin Portal (Requires Login)
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::redirect('/', '/admin/products')->name('dashboard');

    // Products Management with Dynamic Variant Flow
    Route::resource('products', ProductController::class);

    // Categories Management
    Route::resource('categories', CategoryController::class);
});
