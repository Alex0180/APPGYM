@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Lista de Clientes Registrados</h1>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabla de clientes --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center">
            <thead class="table-dark">
                <tr>
                    <th>Foto</th>
                    <th>Nombre Completo</th>
                    <th>Edad</th>
                    <th>Celular</th>
                    <th>Correo</th>
                    <th>Plan</th>
                    <th>Tipo de Pago</th>
                    <th>Cantidad</th>
                    <th>Boucher</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Fin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cliente)
                    <tr>
                        <td>
                            @if($cliente->foto)
                                <img src="{{ asset('storage/' . $cliente->foto) }}" 
                                     alt="Foto de {{ $cliente->nombres }}" 
                                     width="80" height="80" 
                                     class="rounded-circle">
                            @else
                                <span class="text-muted">Sin foto</span>
                            @endif
                        </td>
                        <td>{{ $cliente->nombres }} {{ $cliente->apellidos }}</td>
                        <td>{{ $cliente->edad }}</td>
                        <td>{{ $cliente->celular }}</td>
                        <td>{{ $cliente->correo ?? 'No registrado' }}</td>
                        <td>{{ ucfirst($cliente->plan) }}</td>
                        <td>{{ ucfirst($cliente->tipo_pago) }}</td>
                        <td>
                            @if($cliente->tipo_pago === 'efectivo')
                                C$ {{ number_format($cliente->cantidad, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($cliente->tipo_pago === 'transferencia')
                                {{ $cliente->baucher }}
                            @else
                                -
                            @endif
                                <td>
                                      {{ $cliente->fecha_inicio ? \Carbon\Carbon::parse($cliente->fecha_inicio)->format('d/m/Y') : '-' }}
                                </td>
                                <td>
                                         {{ $cliente->fecha_fin ? \Carbon\Carbon::parse($cliente->fecha_fin)->format('d/m/Y') : '-' }}
                                </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">No hay clientes registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Botón volver al inicio --}}
    <div class="text-center mt-4">
        <a href="{{ route('index') }}" class="btn btn-primary">Volver al Inicio</a>
    </div>
</div>
@endsection
