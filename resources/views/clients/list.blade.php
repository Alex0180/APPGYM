@extends('layouts.app')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    .whatsapp-btn {
        background-color: #25D366;
        border: none;
        color: white;
    }
    .whatsapp-btn:hover {
        background-color: #128C7E;
        color: white;
    }
    .qr-code {
        max-width: 150px;
        margin: 0 auto;
        display: block;
    }
</style>
@endsection

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Lista de Clientes Registrados</h1>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('list_clients') }}" class="mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="busqueda_nombre" class="form-label">Nombre</label>
                <input type="text" id="busqueda_nombre" name="busqueda_nombre" value="{{ request('busqueda_nombre') }}" class="form-control" placeholder="Buscar por nombre" oninput="this.form.submit()">
            </div>
            <div class="col-md-3">
                <label for="busqueda_apellido" class="form-label">Apellido</label>
                <input type="text" id="busqueda_apellido" name="busqueda_apellido" value="{{ request('busqueda_apellido') }}" class="form-control" placeholder="Buscar por apellido" oninput="this.form.submit()">
            </div>
            <div class="col-md-3">
                <label for="filtro_plan" class="form-label">Plan</label>
                <select id="filtro_plan" name="filtro_plan" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="dia" {{ request('filtro_plan') == 'dia' ? 'selected' : '' }}>Día</option>
                    <option value="semana" {{ request('filtro_plan') == 'semana' ? 'selected' : '' }}>Semana</option>
                    <option value="quincena" {{ request('filtro_plan') == 'quincena' ? 'selected' : '' }}>Quincena</option>
                    <option value="mes" {{ request('filtro_plan') == 'mes' ? 'selected' : '' }}>Mes</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" id="fecha" name="fecha" value="{{ request('fecha') }}" class="form-control" onchange="this.form.submit()">
            </div>
        </div>
    </form>

    {{-- Tabla de clientes --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Foto</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Edad</th>
                    <th>Celular</th>
                    <th>Correo</th>
                    <th>Plan</th>
                    <th>Tipo de Pago</th>
                    <th>Cantidad</th>
                    <th>Boucher</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cliente)
                    <tr>
                        <td>
                            @if($cliente->foto)
                                <img src="{{ asset('storage/' . $cliente->foto) }}" 
                                     alt="Foto de {{ $cliente->nombres }}" 
                                     width="60" height="60" 
                                     class="rounded-circle">
                            @else
                                <span class="text-muted">Sin foto</span>
                            @endif
                        </td>
                        <td>{{ $cliente->nombres }}</td>
                        <td>{{ $cliente->apellidos }}</td>
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
                        </td>
                        <td>{{ $cliente->fecha_inicio ? \Carbon\Carbon::parse($cliente->fecha_inicio)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $cliente->fecha_fin ? \Carbon\Carbon::parse($cliente->fecha_fin)->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">
                            {{-- Botón WhatsApp que redirige a otra vista --}}
                            <a href="{{ route('clientes.whatsapp', $cliente->id) }}" class="btn btn-success btn-sm me-1">
                                <i class="bi bi-whatsapp"></i>
                            </a>

                            {{-- Botón Editar --}}
                            <button type="button" class="btn btn-warning btn-sm me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editarModal{{ $cliente->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            {{-- Modal Editar --}}
                            <div class="modal fade" id="editarModal{{ $cliente->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('clientes.update', $cliente->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de realizar los cambios?');">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar Cliente</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                {{-- Campos del formulario --}}
                                                <div class="mb-2">
                                                    <label class="form-label">Nombre</label>
                                                    <input type="text" name="nombres" class="form-control" value="{{ $cliente->nombres }}" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Apellido</label>
                                                    <input type="text" name="apellidos" class="form-control" value="{{ $cliente->apellidos }}" required>
                                                </div>
                                                <div class="mb-2">
                                                     <label class="form-label">Edad</label>
                                                    <input type="number" name="edad" class="form-control" value="{{ $cliente->edad }}" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Plan</label>
                                                    <select name="plan" class="form-select" required>
                                                        <option value="dia" {{ $cliente->plan=='dia' ? 'selected' : '' }}>Día</option>
                                                        <option value="semana" {{ $cliente->plan=='semana' ? 'selected' : '' }}>Semana</option>
                                                        <option value="quincena" {{ $cliente->plan=='quincena' ? 'selected' : '' }}>Quincena</option>
                                                        <option value="mes" {{ $cliente->plan=='mes' ? 'selected' : '' }}>Mes</option>
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Celular</label>
                                                    <input type="text" name="celular" class="form-control" value="{{ $cliente->celular }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Correo</label>
                                                    <input type="email" name="correo" class="form-control" value="{{ $cliente->correo }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Tipo de Pago</label>
                                                    <select name="tipo_pago" class="form-select" required>
                                                        <option value="efectivo" {{ $cliente->tipo_pago == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                                        <option value="transferencia" {{ $cliente->tipo_pago == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Cantidad</label>
                                                    <input type="number" step="0.01" name="cantidad" class="form-control" value="{{ $cliente->cantidad }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Boucher</label>
                                                    <input type="text" name="baucher" class="form-control" value="{{ $cliente->baucher }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Fecha de Inicio</label>
                                                    <input type="date" name="fecha_inicio" class="form-control" value="{{ $cliente->fecha_inicio ? \Carbon\Carbon::parse($cliente->fecha_inicio)->format('Y-m-d') : '' }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label">Fecha de Fin</label>
                                                    <input type="date" name="fecha_fin" class="form-control" value="{{ $cliente->fecha_fin ? \Carbon\Carbon::parse($cliente->fecha_fin)->format('Y-m-d') : '' }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-warning">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Botón Eliminar --}}
                            <button type="button" class="btn btn-danger btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#eliminarModal{{ $cliente->id }}">
                                <i class="bi bi-trash"></i>
                            </button>

                            {{-- Modal Eliminar --}}
                            <div class="modal fade" id="eliminarModal{{ $cliente->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirmar Eliminación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Seguro que deseas eliminar a <strong>{{ $cliente->nombres }} {{ $cliente->apellidos }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-center">No hay clientes registrados.</td>
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

@section('scripts')
<script>
    // Función para formatear números de teléfono
    function formatPhoneNumber(phone) {
        return phone.replace(/\D/g, '');
    }
</script>
@endsection
