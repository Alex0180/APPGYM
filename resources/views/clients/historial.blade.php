@extends('layouts.app')

@section('content')
<div class="d-flex vh-100 justify-content-center align-items-center" style="background: #1c1c1c;">
    <div class="card p-5 text-center shadow-lg" style="background: rgba(40,40,40,0.95); min-width: 350px;">
        <h1 style="color: #ff5c5c;">Historial de Clientes</h1>
        <p style="color: #ddd;">Aquí se mostrará el historial de los clientes.</p>
        <a href="{{ route('index') }}" class="btn btn-light mt-3">Volver</a>
    </div>
</div>
@endsection
