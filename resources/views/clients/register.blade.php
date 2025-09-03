@extends('layouts.app')

@section('content')
<div class="container vh-100 d-flex flex-column justify-content-center align-items-center">
    <h2 class="mb-4">Registro de Cliente</h2>

    <p>Aquí irá el formulario para registrar clientes.</p>

    <a href="{{ route('index') }}" class="btn btn-primary mt-3">Volver</a>
</div>
@endsection
