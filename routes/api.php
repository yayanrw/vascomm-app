<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Public routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('logIn');
});

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    // Users
    Route::post('/logout', [AuthController::class, 'logout'])->name('logOut');
    Route::post('/user/approval', [UserController::class, 'approval'])->name('userApproval');

    // Products
    Route::resource('/product', ProductController::class);

    // Banners
    Route::resource('/banner', BannerController::class);
});


// 404 handler
Route::fallback(function () {
    return response()->json([
        'status' => false,
        'message' => 'Page not found'
    ], 404);
});
