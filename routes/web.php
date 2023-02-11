<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDataController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});
Route::get('/sms', function () {
    return view('sms');
});

Route::post('/sms', [SMSController::class, 'sendSMS']);
Route::get('/getUserData', [UserDataController::class, 'getUserData']);

Route::group(['middleware' => ['is_login']], function () {
    Route::get('/register', [UserController::class, 'loadRegister']);
    Route::post('/user-registered', [UserController::class, 'registered'])->name('registered');
    Route::get('/referral-register', [UserController::class, 'loadRefferralRegister']);
    Route::get('/email-verification/{token}', [UserController::class, 'emailVerification']);
    Route::get('/login', [UserController::class, 'loadLogin']);
    Route::post('/login', [UserController::class, 'userLogin'])->name('login');
   
});

Route::group(['middleware' => ['is_logout']], function () {
    Route::get('/dashboard', [UserController::class, 'loadDashboard']);
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/referral-track', [UserController::class, 'referralTrack'])->name('referralTrack');
    Route::get('/delete-account', [UserController::class, 'deleteAccount'])->name('deleteAccount');
});
