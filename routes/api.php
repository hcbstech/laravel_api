<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\AccountController;
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
Route::post('countries', [ProfileController::class,'getCountries']);
Route::post('states', [ProfileController::class,'getStates']);
Route::post('cities', [ProfileController::class,'getCities']);

Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('get-profile/{id?}', [ProfileController::class,'getProfile']);
    Route::post('update-profile', [ProfileController::class,'update']);
    Route::post('video-profile-update', [AccountController::class,'updateVideoProfile']);
    Route::post('update-gallery', [AccountController::class,'updateGallery']);
    Route::post('get-users-search', [AccountController::class,'getUsersBySearch']);
    Route::get('get-gallery/{id?}', [AccountController::class,'getGallary']);
    Route::get('get-users', [AccountController::class,'getUsers']);
    Route::get('get-meetings', [MeetingController::class,'getMeetings']);
    Route::get('pending-meetings', [MeetingController::class,'pendingMeetings']);
    Route::get('sent-meetings', [MeetingController::class,'sentMeetings']);
    Route::post('store-meeting', [MeetingController::class,'store']);
    Route::post('action-meeting', [MeetingController::class,'actionMeeting']);
});

  