@extends('layouts.app')

@section('content')
<div class="d-flex vh-100 justify-content-center align-items-center" style="background: #1c1c1c;">
    <div class="card p-5 text-center shadow-lg" style="background: rgba(40,40,40,0.95); min-width: 350px; max-width: 450px;">
        <h1 style="color: #ff5c5c; font-weight: 700; margin-bottom: 1rem;">Lectores QR y Facial</h1>
        <p style="color: #ddd; font-size: 1rem; margin-bottom: 2rem;">Aquí gestionaremos los lectores de QR y reconocimiento facial.</p>

        <div class="d-flex flex-column gap-3 mb-4">
            <a href="{{ route('lector_qr') }}" 
               class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2" 
               style="border-radius: 50px; font-weight: 600; padding: 0.8rem 1.5rem;">
                <i class="bi bi-qr-code-scan"></i> Lector QR
            </a>

            <a href="{{ route('lector_facial') }}" 
               class="btn btn-success btn-lg d-flex align-items-center justify-content-center gap-2" 
               style="border-radius: 50px; font-weight: 600; padding: 0.8rem 1.5rem;">
                <i class="bi bi-person-badge"></i> Lector Facial
            </a>

            <a href="{{ route('index') }}" 
               class="btn btn-light btn-lg d-flex align-items-center justify-content-center gap-2 mt-2" 
               style="border-radius: 50px; font-weight: 600; padding: 0.8rem 1.5rem;">
                <i class="bi bi-arrow-left-circle"></i> Volver al inicio
            </a>
        </div>
    </div>
</div>
@endsection
