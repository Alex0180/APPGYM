@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" 
     style="background-color: #bdc3c7; font-family: 'Poppins', sans-serif;">
    
    <div class="container py-5">
        <h1 class="text-center mb-4 fw-bold" style="color: #2c3e50; letter-spacing: 1px;">
            📋 Registrar Clientes
        </h1>

        <div class="card shadow-lg border-0 rounded-4 mx-auto" 
             style="max-width: 700px; background: #ffffff;">
            
            <div class="card-body p-5">
                <form method="POST" action="{{ route('store_client') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Campos -->
                    @foreach(['nombres'=>'Nombres','apellidos'=>'Apellidos','edad'=>'Edad','celular'=>'Número de Celular'] as $id=>$label)
                        <div class="mb-3">
                            <label for="{{ $id }}" class="form-label fw-semibold text-secondary">{{ $label }}</label>

                            @if($id === 'celular')
                                <!-- Campo con banderitas -->
                                <input type="tel" 
                                       class="form-control rounded-3 shadow-sm" 
                                       id="celular" name="celular" required>
                            @else
                                <input type="{{ $id==='edad'?'number':'text' }}" 
                                       class="form-control rounded-3 shadow-sm" 
                                       id="{{ $id }}" name="{{ $id }}" 
                                       {{ $id==='edad'?'min=1':'' }} required>
                            @endif
                        </div>
                    @endforeach

                    <!-- Correo -->
                    <div class="mb-3">
                        <label for="correo" class="form-label fw-semibold text-secondary">
                            Correo <span class="text-danger">(opcional)</span>
                        </label>
                        <input type="email" class="form-control rounded-3 shadow-sm" id="correo" name="correo">
                    </div>

                    <!-- Plan -->
                    <div class="mb-3">
                        <label for="plan" class="form-label fw-semibold text-secondary">¿Desea Pagar?</label>
                        <select class="form-select rounded-3 shadow-sm" id="plan" name="plan" required>
                            <option value="">Seleccione...</option>
                            <option value="dia">Día</option>
                            <option value="semana">Semana</option>
                            <option value="quincena">Quincena</option>
                            <option value="mes">Mes</option>
                        </select>
                    </div>

                    <!-- Tipo de Pago -->
                    <div class="mb-3">
                        <label for="tipo_pago" class="form-label fw-semibold text-secondary">Tipo de Pago</label>
                        <select class="form-select rounded-3 shadow-sm" id="tipo_pago" name="tipo_pago" required>
                            <option value="">Seleccione...</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>

                    <!-- Pago en efectivo -->
                    <div class="mb-3 d-none" id="efectivo_field">
                        <label for="cantidad" class="form-label fw-semibold text-secondary">Cantidad en Córdobas</label>
                        <input type="number" class="form-control rounded-3 shadow-sm" id="cantidad" name="cantidad" min="1">
                    </div>

                    <!-- Pago por transferencia -->
                    <div class="mb-3 d-none" id="transferencia_field">
                        <label for="baucher" class="form-label fw-semibold text-secondary">Número de Baucher</label>
                        <input type="text" class="form-control rounded-3 shadow-sm" id="baucher" name="baucher">
                    </div>

                    <!-- Foto del cliente -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Foto del Cliente</label>
                        <div class="text-center">
                            <video id="video" width="300" height="225" autoplay class="d-none rounded-3 border shadow-sm"></video>
                        </div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-morado-fancy px-4 me-2" id="openCamera">
                                📷 Abrir Cámara
                            </button>
                            <button type="button" class="btn btn-verde-fancy px-4 d-none" id="snap">
                                📸 Tomar Foto
                            </button>
                        </div>
                        <canvas id="canvas" width="300" height="225" class="d-none mt-3 rounded-3 border shadow"></canvas>
                        <input type="hidden" name="foto" id="foto_input">
                    </div>

                    <!-- Botones -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('index') }}" class="btn btn-rojo-fancy px-4 py-2">Cancelar</a>
                        <button type="submit" class="btn btn-azul-fancy px-4 py-2">💾 Guardar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CSS de intl-tel-input -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css"/>


<style>
.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.2rem rgba(59,130,246,0.25);
}

/* BOTONES PREMIUM CON DEGRADADO Y REFLEJO ANIMADO */
.btn-rojo-fancy, .btn-azul-fancy, .btn-morado-fancy, .btn-verde-fancy {
    position: relative;
    color: white;
    font-weight: 600;
    border-radius: 12px;
    padding: 0.55rem 1.5rem;
    text-align: center;
    overflow: hidden;
    transition: all 0.4s ease;
    box-shadow: 0 8px 20px rgba(0,0,0,0.25), inset 0 -3px 6px rgba(0,0,0,0.2);
}

/* Reflejo animado continuo */
.btn-rojo-fancy::before,
.btn-azul-fancy::before,
.btn-morado-fancy::before,
.btn-verde-fancy::before {
    content: '';
    position: absolute;
    top: -100%;
    left: -75%;
    width: 50%;
    height: 300%;
    background: rgba(255,255,255,0.3);
    transform: rotate(25deg);
    animation: shine 2s infinite;
}

@keyframes shine {
    0% { left: -75%; }
    50% { left: 120%; }
    100% { left: -75%; }
}

/* Degradados vibrantes */
.btn-rojo-fancy { background: linear-gradient(145deg, #ef4444, #f87171); }
.btn-azul-fancy { background: linear-gradient(145deg, #3b82f6, #60a5fa); }
.btn-morado-fancy { background: linear-gradient(145deg, #8b5cf6, #a78bfa); }
.btn-verde-fancy { background: linear-gradient(145deg, #22c55e, #4ade80); }

/* Hover con movimiento 3D */
.btn-rojo-fancy:hover, .btn-azul-fancy:hover, .btn-morado-fancy:hover, .btn-verde-fancy:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 12px 25px rgba(0,0,0,0.35), inset 0 -4px 8px rgba(0,0,0,0.25);
}
</style>



<!-- JS de intl-tel-input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>

<script>
    document.getElementById('tipo_pago').addEventListener('change', function () {
        let efectivoField = document.getElementById('efectivo_field');
        let transferenciaField = document.getElementById('transferencia_field');

        efectivoField.classList.add('d-none');
        transferenciaField.classList.add('d-none');

        if (this.value === 'efectivo') {
            efectivoField.classList.remove('d-none');
        } else if (this.value === 'transferencia') {
            transferenciaField.classList.remove('d-none');
        }
    });

    // Cámara
    document.addEventListener("DOMContentLoaded", function () {
        let video = document.getElementById("video");
        let canvas = document.getElementById("canvas");
        let openCamera = document.getElementById("openCamera");
        let snap = document.getElementById("snap");
        let fotoInput = document.getElementById("foto_input");
        let stream = null;

        openCamera.addEventListener("click", function () {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function (mediaStream) {
                        stream = mediaStream;
                        video.srcObject = stream;
                        video.classList.remove("d-none");
                        snap.classList.remove("d-none");
                        openCamera.classList.add("d-none");
                    })
                    .catch(function (err) {
                        alert("Error al acceder a la cámara: " + err);
                    });
            } else {
                alert("Tu navegador no soporta cámara");
            }
        });

        snap.addEventListener("click", function () {
            let context = canvas.getContext("2d");
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.classList.remove("d-none");
            fotoInput.value = canvas.toDataURL("image/png");

            if (stream) {
                let tracks = stream.getTracks();
                tracks.forEach(track => track.stop());
            }

            video.classList.add("d-none");
            snap.classList.add("d-none");
            openCamera.classList.remove("d-none");
        });

        // Campo teléfono con banderitas
        const input = document.querySelector("#celular");
        if(input){
            const iti = window.intlTelInput(input, {
                initialCountry: "ni", // Nicaragua por defecto
                preferredCountries: ["ni", "cr", "hn", "sv", "pa", "mx"], // países preferidos
                separateDialCode: true, // Muestra el +505 separado
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
            });

            // Guardar número con código internacional
            const form = input.closest("form");
            form.addEventListener("submit", function() {
                input.value = iti.getNumber();
            });
        }
    });
</script>
@endsection
