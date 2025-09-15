<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\WhatsAppController;

// Rutas de autenticación
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/index', [AuthController::class, 'index'])->name('index');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de clientes
Route::get('/register-client', [ClientController::class, 'register'])->name('register_client');
Route::post('/register-client', [ClientController::class, 'store'])->name('store_client');
Route::get('/list-clients', [ClientController::class, 'list'])->name('list_clients');

// CRUD de clientes
Route::get('/clients/{id}', [ClientController::class, 'show'])->name('clientes.show');
Route::get('/clients/{id}/edit', [ClientController::class, 'edit'])->name('clientes.edit');
Route::delete('/clients/{id}', [ClientController::class, 'destroy'])->name('clientes.destroy');
Route::put('/clients/{id}', [ClientController::class, 'update'])->name('clientes.update');

// Foto del registro del cliente
Route::get('/photo', function () {
    return view('clients.photo');
})->name('photo');

// Rutas de WhatsApp y QR
Route::get('/clientes/{id}/whatsapp', [WhatsAppController::class, 'enviar'])->name('clientes.whatsapp');
Route::get('/clientes/{id}/descargar-qr', [WhatsAppController::class, 'descargarQR'])->name('clientes.descargar-qr');
Route::match(['get', 'post'], '/clientes/{id}/enviar-con-qr', [WhatsAppController::class, 'enviarConQR'])->name('clientes.enviar-con-qr');

