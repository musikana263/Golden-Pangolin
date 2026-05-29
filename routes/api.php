<?php

use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\DestinationApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API endpoints
Route::prefix('v1')->group(function () {
    // Destinations
    Route::get('destinations', [DestinationApiController::class, 'index']);
    Route::get('destinations/featured', [DestinationApiController::class, 'featured']);
    Route::get('destinations/search', [DestinationApiController::class, 'search']);
    Route::get('destinations/{destination}', [DestinationApiController::class, 'show']);

    // Categories
    Route::get('categories', [CategoryApiController::class, 'index']);
    Route::get('categories/{category}', [CategoryApiController::class, 'show']);
});

// Note: authenticated API endpoints were removed because the legacy `auth:api`
// guard (Laravel 5 `token` driver) no longer exists in Laravel 11. Re-introduce
// with `laravel/sanctum` if you need authenticated API access in the future.
