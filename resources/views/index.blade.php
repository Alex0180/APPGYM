@extends('layouts.app')

@section('content')
<div class="container vh-100 d-flex flex-column justify-content-center align-items-center">
    <h1 class="mb-5">Bienvenido{{ session('username') ? ', ' . e(session('username')) : '' }}</h1>

    <div class="d-flex gap-3">
        <a href="{{ route('register_client') }}" class="btn btn-success btn-lg">Registrar Cliente</a>
        <a href="{{ route('list_clients') }}" class="btn btn-warning btn-lg">Lista de Clientes</a>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button class="btn btn-secondary">Cerrar sesión</button>
    </form>
</div>
@endsection
