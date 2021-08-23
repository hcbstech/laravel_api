<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

    Route::post('login', [LoginController::class ,'login']);
    Route::post('signup', [RegisterController::class ,'signup']);
    Route::post('otp-verify', [LoginController::class,'otpVerify']);

    Route::group(['middleware' => 'auth:sanctum'], function(){
        Route::get('get-profile', [ProfileController::class,'getProfile']);
        Route::post('update-profile', [ProfileController::class,'update']);
    });

  