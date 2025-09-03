@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-4 text-primary fw-bold">Registrar Clientes</h1>

    <div class="card shadow-lg p-4">
        <form method="POST" action="{{ route('store_client') }}" enctype="multipart/form-data">
            @csrf


            <!-- Nombres -->
            <div class="mb-3">
                <label for="nombres" class="form-label">Nombres</label>
                <input type="text" class="form-control" id="nombres" name="nombres" required>
            </div>

            <!-- Apellidos -->
            <div class="mb-3">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
            </div>

            <!-- Edad -->
            <div class="mb-3">
                <label for="edad" class="form-label">Edad</label>
                <input type="number" class="form-control" id="edad" name="edad" min="1" required>
            </div>

            <!-- Número de Celular -->
            <div class="mb-3">
                <label for="celular" class="form-label">Número de Celular</label>
                <input type="text" class="form-control" id="celular" name="celular" required>
            </div>

            <!-- Correo -->
            <div class="mb-3">
                <label for="correo" class="form-label">
                    Correo <span class="text-danger">(opcional)</span>
                </label>
                <input type="email" class="form-control" id="correo" name="correo">
            </div>

            <!-- ¿Desea Pagar? -->
            <div class="mb-3">
                <label for="plan" class="form-label">¿Desea Pagar?</label>
                <select class="form-select" id="plan" name="plan" required>
                    <option value="">Seleccione...</option>
                    <option value="dia">Día</option>
                    <option value="semana">Semana</option>
                    <option value="quincena">Quincena</option>
                    <option value="mes">Mes</option>
                </select>
            </div>

            <!-- Tipo de Pago -->
            <div class="mb-3">
                <label for="tipo_pago" class="form-label">Tipo de Pago</label>
                <select class="form-select" id="tipo_pago" name="tipo_pago" required>
                    <option value="">Seleccione...</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                </select>
            </div>

            <!-- Pago en efectivo -->
            <div class="mb-3 d-none" id="efectivo_field">
                <label for="cantidad" class="form-label">Cantidad en Córdobas</label>
                <input type="number" class="form-control" id="cantidad" name="cantidad" min="1">
            </div>

            <!-- Pago por transferencia -->
            <div class="mb-3 d-none" id="transferencia_field">
                <label for="baucher" class="form-label">Número de Baucher</label>
                <input type="text" class="form-control" id="baucher" name="baucher">
            </div>

            <!-- Foto del cliente -->
             <div class="mb-3">
                 <label class="form-label">Foto del Cliente</label>

             <!-- Video oculto (se mostrará solo después de pedir permisos) -->
                <div class="text-center">
                    <video id="video" width="300" height="225" autoplay class="d-none"></video>
                 </div>

             <!-- Botón para abrir la cámara -->
              <div class="text-center mt-2">
                 <button type="button" class="btn btn-success" id="openCamera">Abrir Cámara</button>
              <button type="button" class="btn btn-primary d-none" id="snap">Tomar Foto</button>
             </div>

             <!-- Canvas donde se mostrará la foto tomada -->
              <canvas id="canvas" width="300" height="225" class="d-none"></canvas>

            <!-- Input oculto para guardar la foto en base64 -->
             <input type="hidden" name="foto" id="foto_input">
            </div>




            <!-- Botones -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success">Guardar Cliente</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Mostrar/ocultar campos según tipo de pago
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
</script>





<script>
document.addEventListener("DOMContentLoaded", function () {
    let video = document.getElementById("video");
    let canvas = document.getElementById("canvas");
    let openCamera = document.getElementById("openCamera");
    let snap = document.getElementById("snap");
    let fotoInput = document.getElementById("foto_input");
    let stream = null;

    // Al hacer clic en "Abrir Cámara"
    openCamera.addEventListener("click", function () {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (mediaStream) {
                    stream = mediaStream;
                    video.srcObject = stream;
                    video.classList.remove("d-none");
                    snap.classList.remove("d-none");
                    openCamera.classList.add("d-none"); // ocultamos "Abrir Cámara"
                })
                .catch(function (err) {
                    alert("Error al acceder a la cámara: " + err);
                });
        } else {
            alert("Tu navegador no soporta cámara");
        }
    });

    // Al hacer clic en "Tomar Foto"
    snap.addEventListener("click", function () {
        let context = canvas.getContext("2d");
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Mostrar la foto tomada
        canvas.classList.remove("d-none");

        // Guardar la foto en Base64 en el input hidden
        let dataURL = canvas.toDataURL("image/png");
        fotoInput.value = dataURL;

        // Detener la cámara después de tomar la foto
        if (stream) {
            let tracks = stream.getTracks();
            tracks.forEach(track => track.stop());
        }

        video.classList.add("d-none");
        snap.classList.add("d-none");
        openCamera.classList.remove("d-none"); // permitir abrir cámara otra vez si se desea
    });
});
</script>


@endsection
