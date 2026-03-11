<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BidController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Public
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category:slug}/items', [CategoryController::class, 'items'])
    ->name('categoryItems');
Route::get('/items/{item}', [ItemController::class, 'show']);

// Authenticated
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/items/{item}/bids', [BidController::class, 'store']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::get('/user/listings', [UserController::class, 'listings'])
        ->name('user.listings');
    Route::get('/user/bids', [UserController::class, 'bids'])
        ->name('userBids');
    Route::get('/user', [UserController::class, 'show']);
});
