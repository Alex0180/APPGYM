<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación Facial - Gimnasio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.11.0/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding-top: 20px;
            padding-bottom: 50px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            border: none;
        }
        .camera-container {
            position: relative;
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 10px;
        }
        #video {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transform: scaleX(-1); /* Espejo para mejor experiencia de usuario */
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: none;
        }
        .face-guide {
            width: 70%;
            height: 70%;
            border: 3px dashed rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            opacity: 0.7;
            box-shadow: 0 0 0 400px rgba(0, 0, 0, 0.3);
        }
        .btn-verify {
            margin-top: 20px;
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 50px;
            font-weight: bold;
            background: linear-gradient(to right, #1a2a6c, #b21f1f);
            border: none;
            transition: all 0.3s;
        }
        .btn-verify:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .status-indicator {
            height: 20px;
            width: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }
        .status-ready { background-color: #28a745; }
        .status-processing { background-color: #ffc107; }
        .status-error { background-color: #dc3545; }
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .result-card {
            transition: all 0.5s ease;
        }
        .welcome-header {
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .instructions {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            color: white;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="welcome-header display-5 fw-bold">ACCESO CON LECTOR FACIAL</h1>
            <p class="lead text-light">Muestra tu rostro dentro del círculo para ingresar al gimnasio</p>
            
            <div class="instructions">
                <h5>Instrucciones:</h5>
                <ol class="list-inline">
                    <li class="list-inline-item">1. Asegúrate de tener buena iluminación</li> |
                    <li class="list-inline-item">2. Colócate frente a la cámara</li> |
                    <li class="list-inline-item">3. Presiona "Verificar Rostro"</li>
                </ol>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <div class="camera-container mb-4">
                            <video id="video" autoplay playsinline></video>
                            <div class="overlay">
                                <div class="face-guide"></div>
                            </div>
                        </div>
                        <canvas id="canvas" style="display: none;"></canvas>
                        
                        <div class="text-center">
                            <div id="status" class="mb-3">
                                <span class="status-indicator status-ready" id="status-icon"></span>
                                <span id="status-text">Cámara lista para verificación</span>
                            </div>
                            
                            <button id="btnVerificar" class="btn btn-primary btn-lg btn-verify">
                                Verificar Rostro
                            </button>
                            
                            <div id="loading" class="mt-4" style="display: none;">
                                <div class="loader"></div>
                                <p class="mt-2">Procesando rostro, por favor espere...</p>
                            </div>
                            
                            <div id="resultado" class="mt-4 result-card"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let stream = null;
        let isProcessing = false;
        let modelsLoaded = false;

        // Elementos DOM
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const btnVerificar = document.getElementById('btnVerificar');
        const resultado = document.getElementById('resultado');
        const statusIcon = document.getElementById('status-icon');
        const statusText = document.getElementById('status-text');
        const loading = document.getElementById('loading');

        // Cargar modelos de FaceAPI.js
        async function loadModels() {
            try {
                updateStatus('processing', 'Cargando modelos de reconocimiento...');
                
                // Cargar los modelos necesarios
                await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
                await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
                await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
                
                modelsLoaded = true;
                updateStatus('ready', 'Modelos cargados. Cámara lista para verificación');
                console.log('Modelos de FaceAPI cargados correctamente');
            } catch (error) {
                console.error('Error cargando modelos:', error);
                updateStatus('error', 'Error cargando modelos de reconocimiento');
                resultado.innerHTML = `
                    <div class="alert alert-danger">
                        Error cargando los modelos de reconocimiento facial. Por favor, recarga la página.
                    </div>
                `;
            }
        }

        // Inicializar cámara
        async function initCamera() {
            try {
                updateStatus('processing', 'Iniciando cámara...');
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    } 
                });
                video.srcObject = stream;
                updateStatus('ready', 'Cámara lista para verificación');
            } catch (err) {
                console.error('Error al acceder a la cámara:', err);
                updateStatus('error', 'No se pudo acceder a la cámara: ' + err.message);
                resultado.innerHTML = `
                    <div class="alert alert-danger">
                        Error al acceder a la cámara. Asegúrate de permitir el acceso a la cámara y que tu dispositivo tenga una cámara funcionando.
                    </div>
                `;
            }
        }

        // Actualizar estado de la interfaz
        function updateStatus(status, message) {
            statusIcon.className = 'status-indicator status-' + status;
            statusText.textContent = message;
        }

        // Detectar rostro y extraer descriptor
        async function getFaceDescriptor(imageElement) {
            if (!modelsLoaded) {
                throw new Error('Modelos de reconocimiento facial no cargados');
            }
            
            // Detectar rostros en la imagen
            const detections = await faceapi
                .detectSingleFace(imageElement, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptor();
                
            if (!detections) {
                throw new Error('No se detectó ningún rostro en la imagen');
            }
            
            return detections.descriptor;
        }

        // Capturar foto y enviar para verificación
        async function verificarRostro() {
            if (isProcessing || !modelsLoaded) return;
            
            isProcessing = true;
            updateStatus('processing', 'Procesando imagen...');
            btnVerificar.disabled = true;
            loading.style.display = 'block';
            resultado.innerHTML = '';
            
            try {
                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                // Obtener descriptor facial
                const faceDescriptor = await getFaceDescriptor(video);
                
                // Convertir el descriptor a array simple para enviarlo al servidor
                const descriptorArray = Array.from(faceDescriptor);
                
                // Enviar al servidor para comparación
                const response = await fetch('{{ route("verificar.facial") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        descriptor: descriptorArray 
                    })
                });
                
                const data = await response.json();
                
                if (data.acceso) {
                    resultado.innerHTML = `
                        <div class="alert alert-success">
                            <h4 class="alert-heading">¡Acceso permitido!</h4>
                            <p>${data.mensaje}</p>
                            <hr>
                            <p class="mb-0">Bienvenido al gimnasio.</p>
                        </div>
                    `;
                    updateStatus('ready', 'Verificación exitosa');
                    
                    // Redirigir después de 3 segundos en caso de acceso exitoso
                    setTimeout(() => {
                        window.location.href = '/acceso-permitido'; // Cambia por tu ruta
                    }, 3000);
                } else {
                    resultado.innerHTML = `
                        <div class="alert alert-danger">
                            <h4 class="alert-heading">Acceso denegado</h4>
                            <p>${data.mensaje}</p>
                        </div>
                    `;
                    updateStatus('ready', 'Verificación fallida');
                }
            } catch (error) {
                console.error('Error:', error);
                resultado.innerHTML = `
                    <div class="alert alert-danger">
                        ${error.message || 'Error en el proceso de verificación. Por favor, intenta nuevamente.'}
                    </div>
                `;
                updateStatus('error', 'Error de verificación');
            } finally {
                isProcessing = false;
                btnVerificar.disabled = false;
                loading.style.display = 'none';
            }
        }

        // Event Listeners
        btnVerificar.addEventListener('click', verificarRostro);

        // Inicializar cámara y modelos al cargar la página
        document.addEventListener('DOMContentLoaded', async () => {
            await initCamera();
            await loadModels();
        });
    </script>
</body>
</html>