@extends('layouts.app')

@section('content')
<div class="d-flex vh-100 align-items-center justify-content-center" style="background: linear-gradient(135deg, #a99d9d, #1a1a1a);">
    <div class="card shadow-lg rounded-4" style="width: 400px; border: none; background: rgba(0,0,0,0.65); box-shadow: 0 0 20px rgba(255,46,46,0.5);">
        <div class="card-body p-5">

            <div class="text-center mb-4">
                <!-- Logo moderno (SVG) -->
                <svg width="80" height="80" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="filter: drop-shadow(0 0 5px #ff2e2e);">
                    <rect x="2" y="26" width="8" height="12" rx="2" fill="#ff2e2e"></rect>
                    <rect x="54" y="26" width="8" height="12" rx="2" fill="#ff2e2e"></rect>
                    <rect x="10" y="28" width="44" height="8" rx="4" fill="#ff4d4d"></rect>
                    <circle cx="8" cy="32" r="6" fill="#ff2e2e"></circle>
                    <circle cx="56" cy="32" r="6" fill="#ff2e2e"></circle>
                </svg>
                <h2 class="mt-3 fw-bold" style="color: #fff; text-shadow: 1px 1px 5px #ea4d4d;">APPGYM</h2>
            </div>

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="mb-3 position-relative">
                    <label for="username" class="form-label fw-semibold" style="color: #fff;">Usuario</label>
                    <input type="text" id="username" name="username" class="form-control ps-5" value="{{ old('username') }}" placeholder="Ingresa tu usuario" required autofocus
                        style="background: rgba(255,255,255,0.05); border: 1px solid #ff2e2e; color: #fff; transition: all 0.3s;">
                    <i class="bi bi-person-fill position-absolute" style="left: 15px; top: 38px; color: #fff;"></i>
                </div>

                <div class="mb-3 position-relative">
                    <label for="password" class="form-label fw-semibold" style="color: #fff;">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control ps-5" placeholder="********" required
                        style="background: rgba(255,255,255,0.05); border: 1px solid #ff2e2e; color: #fff; transition: all 0.3s;">
                    <i class="bi bi-lock-fill position-absolute" style="left: 15px; top: 38px; color: #fff;"></i>
                </div>

                @if($errors->has('credentials'))
                    <div class="text-warning mb-3">{{ $errors->first('credentials') }}</div>
                @endif

                <div class="d-grid">
                    <button type="submit" class="btn btn-lg fw-bold" style="background: linear-gradient(90deg, #ec3131, #ff4d4d); border: none; color: #fff; box-shadow: 0 0 15px #ff2e2e; transition: all 0.3s;">
                        Iniciar sesión
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Iconos Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<!-- Hover efecto para botón -->
<style>
    .btn:hover {
        box-shadow: 0 0 25px #ff2e2e, 0 0 50px #ff4d4d;
        transform: translateY(-2px);
    }
    input:focus {
        border-color: #ff4d4d;
        box-shadow: 0 0 10px #ff2e2e;
        background: rgba(255,255,255,0.1);
        color: #fff;
    }
</style>
@endsection
