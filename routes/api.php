<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Support\Facades\Route;

// --- Public Routes ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/slug/{slug}', [ProductController::class, 'show']);

// --- Authenticated Routes ---
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Orders (Client/Employee/Admin)
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    // --- Employee & Admin Routes ---
    Route::middleware('role:employe,admin')->group(function () {
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    });

    // --- Admin Only Routes ---
    Route::middleware('role:admin')->group(function () {
        // User Management
        Route::get('/admin/users', [AdminController::class, 'getAllUsers']);
        Route::get('/admin/users/{id}', [AdminController::class, 'getUserById']);
        Route::put('/admin/users/{id}/render-employee', [AdminController::class, 'renderUserEmployee']);
        
        // Stats
        Route::get('/admin/stats', [AdminController::class, 'getStats']);

        // Category Management
        Route::post('/admin/categories', [CategoryController::class, 'store']);
        Route::put('/admin/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/admin/categories/{id}', [CategoryController::class, 'destroy']);

        // Product Management
        Route::post('/admin/products', [ProductController::class, 'store']);
        Route::put('/admin/products/{id}', [ProductController::class, 'update']);
        Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);
    });
});