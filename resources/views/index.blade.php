@extends('layouts.app')

@section('content')
<div class="d-flex vh-100 justify-content-center align-items-center" 
     style="background: linear-gradient(160deg, #1c1c1c, #330000); font-family: 'Poppins', sans-serif;">

    <div class="card rounded-4 p-5 text-center shadow-lg" 
         style="background: rgba(40,40,40,0.95); min-width: 350px; max-width: 450px;">

        <h1 class="mb-3" 
            style="color: #ff5c5c; font-weight: 700; font-size: 2.2rem; font-family: 'Montserrat', sans-serif;">
            Bienvenido
        </h1>

        <p class="mb-4" style="color: #ddd; font-size: 1rem; font-family: 'Poppins', sans-serif;">
            Selecciona una acción para gestionar tu gimnasio
        </p>

        <div class="d-flex flex-column gap-3 mb-4">
            <a href="{{ route('register_client') }}" 
               class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2" 
               style="border-radius: 50px; font-weight: 600; padding: 0.8rem 1.5rem; font-family: 'Poppins', sans-serif;">
                <i class="bi bi-person-plus-fill"></i> Registrar Cliente
            </a>
            <a href="{{ route('list_clients') }}" 
               class="btn btn-warning btn-lg d-flex align-items-center justify-content-center gap-2" 
               style="border-radius: 50px; font-weight: 600; padding: 0.8rem 1.5rem; font-family: 'Poppins', sans-serif;">
                <i class="bi bi-people-fill"></i> Lista de Clientes
            </a>
            <a href="{{ route('lectores') }}" 
               class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2" 
               style="border-radius: 50px; font-weight: 600; padding: 0.8rem 1.5rem; font-family: 'Poppins', sans-serif;">
                <i class="bi bi-qr-code-scan"></i> Lectores QR y Facial
            </a>
            <a href="{{ route('historial') }}" 
               class="btn btn-info btn-lg d-flex align-items-center justify-content-center gap-2" 
               style="border-radius: 50px; font-weight: 600; padding: 0.8rem 1.5rem; font-family: 'Poppins', sans-serif;">
                <i class="bi bi-clock-history"></i> Historial
            </a>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-secondary btn-lg d-flex align-items-center justify-content-center gap-2" 
                    style="border-radius: 50px; padding: 0.8rem 1.5rem; font-family: 'Poppins', sans-serif;">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </button>
        </form>
    </div>
</div>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<!-- Iconos Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .btn-danger:hover {
        background-color: #cc3b3b;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(204,59,59,0.4);
    }
    .btn-warning:hover {
        background-color: #e6b800;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(230,184,0,0.4);
    }
    .btn-secondary:hover {
        background-color: #555;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .btn-primary:hover {
        background-color: #004085;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,64,133,0.4);
    }
    .btn-info:hover {
        background-color: #17a2b8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(23,162,184,0.4);
    }
</style>
@endsection
