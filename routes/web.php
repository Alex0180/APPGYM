<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/index', [AuthController::class, 'index'])->name('index');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\ClientController;

Route::get('/register-client', [ClientController::class, 'register'])->name('register_client');
Route::get('/list-clients', [ClientController::class, 'list'])->name('list_clients');
