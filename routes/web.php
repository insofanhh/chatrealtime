<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::resource('profile', UserController::class);

Route::middleware('authentication')->group(function () {
    Route::get('/chat', [ChatController::class, 'chat'])->name('chat');
    Route::post('/send-message', [ChatController::class, 'sendMessage'])->name('sendMessage');
});
