@extends('layouts.app')

@section('content')
<div class="container vh-100 d-flex flex-column justify-content-center align-items-center">
    <h2 class="mb-4">Lista de Clientes</h2>

    <p>Aquí se mostrará la lista de clientes registrados.</p>

    <a href="{{ route('index') }}" class="btn btn-primary mt-3">Volver</a>
</div>
@endsection
