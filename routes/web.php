<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [AuthController::class, 'login'])->name(name: 'login');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::get('/forgetPassword', [AuthController::class, 'forgetPassword'])->name('forgetPassword');
Route::get('/verifyOtp', [AuthController::class, 'verifyOtp'])->name('verifyOtp');
Route::get('/resetPassword', [AuthController::class, 'resetPassword'])->name('resetPassword');

Route::post('/saveRegister', [AuthController::class, 'saveRegister'])->name('saveRegister');
Route::post('/checkLogin', [AuthController::class, 'checkLogin'])->name('checkLogin');
Route::post('/sendOtp', [AuthController::class, 'sendOtp'])->name('sendOtp');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/sndVerifyOtp', [AuthController::class, 'sndVerifyOtp'])->name('sndVerifyOtp');
Route::get('/home', [HomeController::class, 'index'])->name('home');