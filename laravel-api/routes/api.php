<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

Route::post('login', [AuthController::class, 'authenticate']);
Route::post('register', [AuthController::class, 'register']);

Route::group(['middleware' => ['jwt.verify','role:ROLE_ADMIN']], function() {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('get_user', [AuthController::class, 'get_user']);
});

Route::get('command', [AuthController::class, 'command']);
Route::get('user1', [AuthController::class, 'user1']);
Route::get('user2', [AuthController::class, 'user2']);
Route::get('user3', [AuthController::class, 'user3']);
Route::get('userTest', [AuthController::class, 'get_user']);