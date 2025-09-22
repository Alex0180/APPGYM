<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lector QR - Solucionado</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 600px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            text-align: center;
            position: relative;
        }
        
        h1 {
            margin-bottom: 20px;
            color: #fff;
            font-size: 2.2rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .logo {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #fdbb2d;
        }
        
        #qr-container {
            position: relative;
            width: 100%;
            margin: 20px 0;
        }
        
        #reader {
            width: 100%;
            max-width: 400px;
            aspect-ratio: 1/1;
            border: 3px solid #fff;
            border-radius: 15px;
            margin: 0 auto;
            overflow: hidden;
            background: #222;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
        
        #result {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            padding: 20px;
            box-sizing: border-box;
            z-index: 10;
            display: none;
            border-radius: 15px;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
        }
        
        .camera-controls {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin: 15px 0;
        }
        
        #camera-selection {
            padding: 10px 15px;
            border-radius: 8px;
            background: #333;
            color: white;
            border: 1px solid #555;
            flex-grow: 1;
            max-width: 300px;
        }
        
        .btn {
            padding: 12px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .btn-light {
            background: #f8f9fa;
            color: #212529;
        }
        
        .btn-light:hover {
            background: #e2e6ea;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .status {
            margin: 15px 0;
            padding: 15px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            font-size: 1.1rem;
        }
        
        .help-text {
            margin-top: 20px;
            color: #ddd;
            font-size: 0.95rem;
            max-width: 500px;
            text-align: left;
            background: rgba(0, 0, 0, 0.4);
            padding: 15px;
            border-radius: 10px;
        }
        
        .help-text ul {
            padding-left: 20px;
            margin-top: 10px;
        }
        
        .help-text li {
            margin-bottom: 8px;
        }
        
        .scan-line {
            position: absolute;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, transparent, #007bff, transparent);
            box-shadow: 0 0 15px #007bff;
            animation: scan 2s infinite linear;
            z-index: 5;
            display: block;
            top: 0;
        }
        
        @keyframes scan {
            0% { top: 0; }
            100% { top: 100%; }
        }
        
        .flash {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: white;
            opacity: 0;
            pointer-events: none;
            z-index: 100;
        }
        
        @keyframes flash {
            0% { opacity: 0; }
            50% { opacity: 0.8; }
            100% { opacity: 0; }
        }
        
        .permission-request {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            text-align: center;
            display: none;
        }
        
        .camera-icon {
            font-size: 3rem;
            color: #ffc107;
            margin-bottom: 15px;
        }
        
        .footer {
            margin-top: 30px;
            color: #ddd;
            font-size: 0.9rem;
        }
        
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        
        /* Responsividad */
        @media (max-width: 768px) {
            .container {
                padding: 20px 15px;
            }
            
            h1 {
                font-size: 1.8rem;
                margin-top: 10px;
            }
            
            .back-button {
                position: relative;
                top: 0;
                left: 0;
                margin-bottom: 15px;
                align-self: flex-start;
            }
            
            .camera-controls {
                flex-direction: column;
                align-items: center;
            }
            
            #camera-selection {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Botón de volver -->
        <div class="back-button">
            <a href="{{ route('lectores') }}" id="volver-btn" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="logo">
            <i class="fas fa-qrcode"></i>
        </div>
        <h1>Lector de Códigos QR</h1>
        
        <div class="permission-request" id="permission-request">
            <div class="camera-icon">
                <i class="fas fa-camera"></i>
            </div>
            <h2>Permiso de cámara requerido</h2>
            <p>Para escanear códigos QR, necesitamos acceso a tu cámara.</p>
            <button id="enable-camera" class="btn btn-success">
                <i class="fas fa-camera"></i> Permitir acceso a la cámara
            </button>
        </div>
        
        <div id="qr-container">
            <div id="reader"></div>
            <div class="scan-line"></div>
            <div id="result"></div>
        </div>
        
        <div class="status" id="status">Preparando cámara frontal...</div>
        
        <div class="camera-controls">
            <select id="camera-selection">
                <option value="">Seleccionar cámara...</option>
            </select>
            <button id="switch-camera" class="btn btn-warning">
                <i class="fas fa-sync-alt"></i> Cambiar cámara
            </button>
        </div>
        
        <div>
            <button id="restart-btn" class="btn">
                <i class="fas fa-redo"></i> Reiniciar Escáner
            </button>
            <button id="toggle-torch" class="btn" style="display: none;">
                <i class="fas fa-lightbulb"></i> Luz
            </button>
        </div>
        
        <div class="help-text">
            <p><i class="fas fa-lightbulb"></i> <strong>Si el escáner no funciona:</strong></p>
            <ul>
                <li>Asegúrate de permitir el acceso a la cámara cuando el navegador lo solicite</li>
                <li>Prueba con mejor iluminación</li>
                <li>Selecciona la cámara frontal manualmente si es necesario</li>
                <li>Acerca el código QR lentamente a la cámara</li>
                <li>Actualiza tu navegador a la última versión</li>
            </ul>
        </div>
    </div>

    <!-- Sonidos -->
    <audio id="sound-ok" src="https://assets.mixkit.co/sfx/preview/mixkit-game-ball-tap-2073.mp3"></audio>
    <audio id="sound-error" src="https://assets.mixkit.co/sfx/preview/mixkit-wrong-answer-fail-notification-946.mp3"></audio>
    
    <!-- Flash para feedback -->
    <div class="flash" id="flash"></div>

    <div class="footer">
        <p>Solucionado problema de cámara frontal | v2.1</p>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        const resultContainer = document.getElementById('result');
        const statusElement = document.getElementById('status');
        const cameraSelection = document.getElementById('camera-selection');
        const restartBtn = document.getElementById('restart-btn');
        const switchCameraBtn = document.getElementById('switch-camera');
        const enableCameraBtn = document.getElementById('enable-camera');
        const permissionRequest = document.getElementById('permission-request');
        const toggleTorchBtn = document.getElementById('toggle-torch');
        const volverBtn = document.getElementById('volver-btn');
        const soundOk = document.getElementById('sound-ok');
        const soundError = document.getElementById('sound-error');
        const flashElement = document.getElementById('flash');

        let html5QrcodeScanner = null;
        let isScanning = false;
        let currentCameraId = null;
        let availableCameras = [];
        let isFrontCamera = true;
        let torchEnabled = false;

        // Efecto de flash para feedback
        function flash(color) {
            flashElement.style.backgroundColor = color;
            flashElement.style.animation = 'none';
            setTimeout(() => {
                flashElement.style.animation = 'flash 0.5s';
            }, 10);
        }

        // Mostrar mensaje de estado
        function setStatus(message, isError = false) {
            statusElement.textContent = message;
            statusElement.style.backgroundColor = isError ? 'rgba(220, 53, 69, 0.3)' : 'rgba(255, 255, 255, 0.1)';
            statusElement.style.color = isError ? '#ff8c9f' : '#fff';
        }

        // Mostrar resultado
        function mostrarMensaje(texto, color, sonido = null) {
            resultContainer.innerText = texto;
            resultContainer.style.backgroundColor = color;
            resultContainer.style.display = "flex";
            
            if (sonido) {
                sonido.currentTime = 0;
                sonido.play();
            }
            
            // Ocultar después de 5 segundos
            setTimeout(() => {
                resultContainer.style.display = "none";
                restartScanner();
            }, 5000);
        }

        // Procesar código QR
        function procesarQR(decodedText) {
            // Detener el escáner momentáneamente
            if (html5QrcodeScanner && isScanning) {
                html5QrcodeScanner.pause();
                isScanning = false;
            }
            
            const texto = decodedText.replace(/\r?\n|\r/g, " ");
            const fechaRegex = /Vence:\s*(\d{2}\/\d{2}\/\d{4})/i;
            const match = texto.match(fechaRegex);

            if (match) {
                const fechaVence = match[1];
                const partes = fechaVence.split('/');
                const fechaObj = new Date(`${partes[2]}-${partes[1]}-${partes[0]}`);
                const hoy = new Date(); 
                hoy.setHours(0, 0, 0, 0); 
                fechaObj.setHours(0, 0, 0, 0);

                if (fechaObj >= hoy) {
                    flash('#28a745');
                    mostrarMensaje("✅ Pase adelante, tu membresía está vigente. ¡Disfruta del gimnasio!", "#28a745", soundOk);
                } else {
                    flash('#dc3545');
                    mostrarMensaje("❌ Lo sentimos, tu membresía ha vencido. Por favor, renueva para ingresar.", "#dc3545", soundError);
                }
            } else {
                flash('#ffc107');
                mostrarMensaje("⚠ Código QR inválido.", "#ffc107", soundError);
            }
        }

        // Enumerar cámaras disponibles
        function listCameras() {
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    availableCameras = cameras;
                    
                    // Limpiar selector
                    cameraSelection.innerHTML = '';
                    
                    // Llenar selector de cámaras
                    cameras.forEach((camera, index) => {
                        const option = document.createElement('option');
                        option.value = camera.id;
                        
                        // Detectar si es cámara frontal
                        const isFront = camera.label.includes('front') || 
                                       camera.label.includes('face') || 
                                       (camera.label.includes('0') && !camera.label.includes('back'));
                                       
                        option.textContent = isFront ? `Cámara frontal (${camera.label})` : 
                                     camera.label.includes('back') ? `Cámara trasera (${camera.label})` : 
                                     `Cámara ${index + 1}: ${camera.label}`;
                        
                        cameraSelection.appendChild(option);
                        
                        // Seleccionar cámara frontal por defecto
                        if (isFront) {
                            option.selected = true;
                            currentCameraId = camera.id;
                            isFrontCamera = true;
                        }
                    });
                    
                    // Si no encontramos cámara frontal, usar la primera disponible
                    if (!currentCameraId && cameras.length > 0) {
                        currentCameraId = cameras[0].id;
                        cameraSelection.options[0].selected = true;
                    }
                    
                    // Iniciar escáner con la cámara seleccionada
                    startWithCamera(currentCameraId);
                } else {
                    setStatus("No se detectó ninguna cámara.", true);
                    permissionRequest.style.display = 'block';
                }
            }).catch(err => {
                setStatus("Error al acceder a la cámara. " + err, true);
                permissionRequest.style.display = 'block';
            });
        }

        // Iniciar con cámara específica
        function startWithCamera(cameraId) {
            if (!cameraId) return;
            
            const config = {
                fps: 15,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                focusMode: "continuous"
            };

            // Si ya hay un escáner activo, detenerlo primero
            if (html5QrcodeScanner && isScanning) {
                html5QrcodeScanner.stop().then(() => {
                    initScanner(cameraId, config);
                }).catch(err => {
                    console.error("Error al detener cámara:", err);
                    initScanner(cameraId, config);
                });
            } else {
                initScanner(cameraId, config);
            }
        }

        function initScanner(cameraId, config) {
            html5QrcodeScanner = new Html5Qrcode("reader");
            
            html5QrcodeScanner.start(
                cameraId,
                config,
                onScanSuccess,
                onScanFailure
            ).then(() => {
                isScanning = true;
                currentCameraId = cameraId;
                setStatus("Escaneando... Acerca el código QR a la cámara.");
                permissionRequest.style.display = 'none';
                
                // Verificar si la cámara tiene flash (suele ser la trasera)
                if (!isFrontCamera) {
                    toggleTorchBtn.style.display = 'inline-block';
                } else {
                    toggleTorchBtn.style.display = 'none';
                    torchEnabled = false;
                }
            }).catch(err => {
                setStatus("Error al iniciar la cámara: " + err, true);
                
                // Si falla con la cámara frontal, intentar con la trasera
                if (isFrontCamera) {
                    setStatus("Intentando con cámara trasera...");
                    isFrontCamera = false;
                    const backCamera = availableCameras.find(cam => 
                        cam.label.includes('back') || !cam.label.includes('front')
                    );
                    
                    if (backCamera) {
                        cameraSelection.value = backCamera.id;
                        startWithCamera(backCamera.id);
                    }
                }
            });
        }

        // Función llamada cuando se escanea un código exitosamente
        function onScanSuccess(decodedText) {
            procesarQR(decodedText);
        }

        // Función llamada cuando falla el escaneo
        function onScanFailure(error) {
            // No es necesario hacer nada para errores normales de escaneo
        }

        // Reiniciar el escáner
        function restartScanner() {
            if (html5QrcodeScanner && !isScanning) {
                html5QrcodeScanner.resume().then(() => {
                    isScanning = true;
                    setStatus("Escaneando...");
                }).catch(err => {
                    // Si no se puede reanudar, reiniciar completamente
                    startWithCamera(currentCameraId);
                });
            }
        }

        // Alternar flash
        function toggleTorch() {
            if (!html5QrcodeScanner) return;
            
            html5QrcodeScanner.getRunningTrackCapabilities().then((capabilities) => {
                if (capabilities.torch) {
                    torchEnabled = !torchEnabled;
                    html5QrcodeScanner.applyVideoConstraints({
                        advanced: [{ torch: torchEnabled }]
                    });
                    toggleTorchBtn.innerHTML = torchEnabled ? 
                        '<i class="fas fa-lightbulb"></i> Apagar luz' : 
                        '<i class="fas fa-lightbulb"></i> Encender luz';
                }
            }).catch(err => {
                console.error("Error al acceder a flash:", err);
            });
        }

        // Event Listeners
        restartBtn.addEventListener('click', () => {
            startWithCamera(currentCameraId);
        });

        switchCameraBtn.addEventListener('click', () => {
            isFrontCamera = !isFrontCamera;
            const targetCamera = availableCameras.find(cam => {
                if (isFrontCamera) {
                    return cam.label.includes('front') || cam.label.includes('face');
                } else {
                    return cam.label.includes('back') || !cam.label.includes('front');
                }
            });
            
            if (targetCamera) {
                cameraSelection.value = targetCamera.id;
                startWithCamera(targetCamera.id);
            }
        });

        cameraSelection.addEventListener('change', () => {
            if (cameraSelection.value) {
                const selectedCamera = availableCameras.find(cam => cam.id === cameraSelection.value);
                isFrontCamera = selectedCamera.label.includes('front') || selectedCamera.label.includes('face');
                startWithCamera(cameraSelection.value);
            }
        });

        enableCameraBtn.addEventListener('click', () => {
            setStatus("Solicitando permiso de cámara...");
            listCameras();
        });

        toggleTorchBtn.addEventListener('click', toggleTorch);

        // Botón de volver - ya tiene el href correcto desde el HTML
        // Solo añadimos funcionalidad para detener la cámara antes de salir
        volverBtn.addEventListener('click', (e) => {
            // Si estamos en modo de demostración, prevenir la navegación
            e.preventDefault();
            
            // Detener la cámara antes de salir
            if (html5QrcodeScanner && isScanning) {
                html5QrcodeScanner.stop().then(() => {
                    console.log("Cámara detenida, redirigiendo...");
                    // Redirigir a la ruta especificada
                    window.location.href = volverBtn.href;
                }).catch(err => {
                    console.error("Error al detener cámara:", err);
                    window.location.href = volverBtn.href;
                });
            } else {
                window.location.href = volverBtn.href;
            }
        });

        // Verificar compatibilidad al cargar
        window.addEventListener('load', () => {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                setStatus("Tu navegador no soporta acceso a la cámara.", true);
                return;
            }
            
            setStatus("Solicitando acceso a la cámara frontal...");
            listCameras();
        });
    </script>
</body>
</html>