@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h2 class="mb-4 text-success">Tomar Foto del Cliente</h2>

    <video id="video" width="320" height="240" autoplay class="border"></video>
    <br>
    <button id="snap" class="btn btn-primary mt-3">Capturar Foto</button>
    <canvas id="canvas" width="320" height="240" class="mt-3 border d-none"></canvas>

    <div class="mt-4">
        <a href="{{ route('register_client') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>

<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const snap = document.getElementById('snap');
    const context = canvas.getContext('2d');

    // pedir permisos de cámara
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            video.srcObject = stream;
        })
        .catch(err => {
            alert("Error al acceder a la cámara: " + err);
        });

    // tomar foto
    snap.addEventListener("click", () => {
        context.drawImage(video, 0, 0, 320, 240);
        canvas.classList.remove("d-none");
    });
</script>
@endsection

