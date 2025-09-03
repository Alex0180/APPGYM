@extends('layouts.app')

@section('content')
<div class="d-flex vh-100">
  <div class="m-auto" style="width: 380px;">
    <div class="text-center mb-4">
      <!-- Logo simple (SVG) -->
      <svg width="80" height="80" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <!-- mancuernas estilizadas -->
        <rect x="2" y="26" width="8" height="12" rx="2"></rect>
        <rect x="54" y="26" width="8" height="12" rx="2"></rect>
        <rect x="10" y="28" width="44" height="8" rx="4"></rect>
        <circle cx="8" cy="32" r="6"></circle>
        <circle cx="56" cy="32" r="6"></circle>
      </svg>
      <h3 class="mt-2">APPGYM</h3>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <form method="POST" action="{{ route('login.post') }}">
          @csrf

          <div class="mb-3">
            <label for="username" class="form-label">Usuario</label>
            <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" required autofocus>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control" required>
          </div>

          @if($errors->has('credentials'))
            <div class="text-danger mb-2">{{ $errors->first('credentials') }}</div>
          @endif

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Iniciar sesión</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
@endsection
