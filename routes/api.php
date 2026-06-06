<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (){
    // auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateMe']);

    // store
    Route::get('/stores', [StoreController::class, 'index']);
    Route::get('/stores/{id}', [StoreController::class, 'show']);
    // admin
    Route::post('/stores', [StoreController::class, 'store']);
    Route::put('/stores/{id}', [StoreController::class, 'update']);
    Route::patch('/stores/{id}/toggle', [StoreController::class, 'toggle']);
    Route::get('/my-store', [StoreController::class, 'myStore']);

    // product
    Route::get('/stores/{storeId}/products', [ProductController::class, 'byStore']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/categories', [ProductController::class, 'categories']);
    // owner
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::patch('/products/{id}/stock', [ProductController::class, 'updateStock']);

    // order
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'myOrders']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/store/orders', [OrderController::class, 'storeOrders']);   //owner
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);  //owner
    Route::patch('/orders/{id}/cancel', [OrderController::class, 'cancel']);   //customer

    // review
    Route::post('/order/{orderId}/reviews', [ReviewController::class, 'store']);   //costumer
    Route::get('/products/{id}/reviews', [ReviewController::class, 'byProduct']);
    // report
    Route::get('/store/reports/revenue', [ReportController::class, 'revenue']);
    Route::get('/store/reports/top-products', [ReportController::class, 'topProducts']);
    Route::get('/store/reports/orders', [ReportController::class, 'orderStats']);

    // admin
    Route::get('/admin/stats', [AdminController::class, 'stats']);
    Route::get('/admin/stores', [AdminController::class, 'stores']);
    Route::patch('/admin/stores/{id}/status', [AdminController::class, 'toggleStore']);
    Route::get('/admin/users', [AdminController::class, 'users']);
    Route::patch('/admin/users/{id}/role', [AdminController::class, 'changeRole']);
});
