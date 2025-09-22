<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar QR por WhatsApp - {{ config('app.name', 'FitZone Gym') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #e9f5ef; }
        .card { border-radius: 1rem; }
        .qr-container {
            position: relative;
            display: inline-block;
            padding: 1rem;
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border: 6px solid #28a745;
        }
        .qr-container img.qr {
            display: block;
            width: 300px;
            height: 300px;
            border-radius: 0.5rem;
        }
        .qr-container img.logo {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 50px; /* Reducido para mejor lectura */
            height: 50px; /* Reducido para mejor lectura */
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: #fff;
            padding: 5px;
            border: 2px solid #28a745; /* Mejor contraste */
        }
        .message-box {
            background: #ffffff;
            border-left: 4px solid #28a745;
            padding: 1rem;
            border-radius: 0.5rem;
            font-size: 0.95rem;
        }
        .warning-box {
            margin-top: 1rem;
            padding: 0.8rem;
            border-radius: 0.5rem;
            background: #ffeaea;
            border-left: 4px solid #dc3545;
            color: #dc3545;
            font-weight: bold;
        }
        .card-footer a, .card-footer button {
            min-width: 140px;
        }
    </style>
</head>
<body>

<div class="container py-5 d-flex justify-content-center">
    <div class="card shadow-lg w-100" style="max-width: 500px;">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-whatsapp me-2"></i> Enviar WhatsApp</h5>
        </div>
        <div class="card-body text-center">
            <h4 class="fw-bold mb-2">{{ $cliente->nombres }} {{ $cliente->apellidos }}</h4>
            <p class="text-muted mb-4">
                Plan: <span class="fw-semibold">{{ ucfirst($cliente->plan) }}</span><br>
                Vence:
                <span class="fw-semibold">
                    {{ $cliente->fecha_fin ? \Carbon\Carbon::parse($cliente->fecha_fin)->format('d/m/Y') : '-' }}
                </span>
            </p>

            @php
                use SimpleSoftwareIO\QrCode\Facades\QrCode;
                use Carbon\Carbon;

                $fechaFin = $cliente->fecha_fin ? Carbon::parse($cliente->fecha_fin) : null;
                $diasRestantes = $fechaFin ? Carbon::now()->diffInDays($fechaFin, false) : null;

                // Determinar texto de vencimiento
                $textoDias = '';
                if ($diasRestantes !== null) {
                    if ($diasRestantes > 0) {
                        $textoDias = "Su membresía vence en " . intval($diasRestantes) . " día" . (intval($diasRestantes) == 1 ? '' : 's') . ".";
                    } elseif ($diasRestantes == 0) {
                        $textoDias = "Su membresía vence hoy.";
                    } else { // $diasRestantes < 0
                        $textoDias = "Su membresía venció hace " . abs(intval($diasRestantes)) . " día" . (abs(intval($diasRestantes)) == 1 ? '' : 's') . ".";
                    }
                }

                // Contenido QR (UTF-8, sin emojis problemáticos)
                $qrContent = "Tu Plan es: " . ucfirst($cliente->plan) . "\n";
                $qrContent .= "Vence: " . ($fechaFin ? $fechaFin->format('d/m/Y') : 'N/A');
                if ($diasRestantes !== null && $diasRestantes <= 3) {
                    $qrContent .= "\n\nATENCIÓN: " . $textoDias . " Le invitamos cordialmente a renovarla.";
                }
                $qrContentUtf8 = mb_convert_encoding($qrContent, 'UTF-8', 'auto');

                // Mejoras para mejor lectura del QR
                $qrSvg = base64_encode(QrCode::format('svg')
                    ->encoding('UTF-8')
                    ->size(300)
                    ->margin(2) // Margen ligeramente aumentado
                    ->errorCorrection('H') // Máxima corrección de errores
                    ->generate($qrContentUtf8));

                // Mensaje WhatsApp
                $mensaje = "Hola {$cliente->nombres}, " . config('app.name', 'Mi Gimnasio') . " le da la bienvenida!" . "\n" .
                "A partir de ahora podrá usar este código QR para ingresar a nuestras instalaciones." . "\n\n" .
                "Su Plan es " . ucfirst($cliente->plan) . " y este Vence: " . ($fechaFin ? $fechaFin->format('d/m/Y') : 'N/A') . "\n\n" .
                "Es un placer que nos haya escogido. ¡Esperamos que su experiencia sea excelente!";

                if ($diasRestantes !== null && $diasRestantes <= 3) {
                    $mensaje .= "\n\n⚠ Atención: " . $textoDias . " Le invitamos cordialmente a renovarla para continuar disfrutando de nuestras instalaciones.";
                }

                $celular = $cliente->celular;
                $urlWhatsApp = "https://wa.me/{$celular}?text=" . urlencode($mensaje);
            @endphp

            <!-- QR -->
            <div class="qr-container my-3" id="qrBox">
                <img id="qrImage" src="data:image/svg+xml;base64,{{ $qrSvg }}" alt="QR {{ $cliente->nombres }}" class="qr">
                <img id="qrLogo" src="{{ asset('images/logo-gym.png') }}" alt="Logo Gym" class="logo">
            </div>

            <!-- Mensaje en pantalla -->
            <div class="message-box mt-3 text-start">
                <i class="bi bi-info-circle-fill text-success me-2"></i>
                {!! nl2br(e($mensaje)) !!}
            </div>

            <!-- Advertencia de vencimiento formal -->
            @if($diasRestantes !== null && $diasRestantes <= 3)
                <div class="warning-box">
                    ⚠ {{ $textoDias }} Le invitamos cordialmente a renovarla para continuar disfrutando de nuestras instalaciones.
                </div>
            @endif
        </div>

        <div class="card-footer d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                <i class="bi bi-x-circle me-1"></i> Cancelar
            </button>

            <!-- Descargar QR -->
            <button id="downloadBtn" class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Descargar QR
            </button>

            <a href="{{ $urlWhatsApp }}" target="_blank" class="btn btn-success">
                <i class="bi bi-whatsapp me-1"></i> Enviar WhatsApp
            </a>
        </div>
    </div>
</div>

<!-- Script exportar QR mejorado -->
<script>
document.getElementById('downloadBtn').addEventListener('click', function() {
    const qrImg = document.getElementById('qrImage');
    const logoImg = document.getElementById('qrLogo');

    const canvas = document.createElement('canvas');
    const size = 320;
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext('2d');

    // Fondo blanco para mejor contraste
    ctx.fillStyle = '#FFFFFF';
    ctx.fillRect(0, 0, size, size);

    const qr = new Image();
    qr.src = qrImg.src;
    qr.onload = function() {
        ctx.drawImage(qr, 0, 0, size, size);

        const logo = new Image();
        logo.crossOrigin = "Anonymous"; // Para evitar problemas con CORS
        logo.src = logoImg.src;
        logo.onload = function() {
            const logoSize = 50; // Reducido para mejor lectura del QR
            // Fondo blanco para el logo
            ctx.beginPath();
            ctx.arc(size/2, size/2, logoSize/2+4, 0, Math.PI*2, true);
            ctx.fillStyle = "#fff";
            ctx.fill();
            // Borde para mejor contraste
            ctx.beginPath();
            ctx.arc(size/2, size/2, logoSize/2+4, 0, Math.PI*2, true);
            ctx.strokeStyle = "#28a745";
            ctx.lineWidth = 2;
            ctx.stroke();
            // Dibujar el logo
            ctx.drawImage(logo, size/2 - logoSize/2, size/2 - logoSize/2, logoSize, logoSize);

            // Mejorar la calidad de la imagen
            const link = document.createElement('a');
            link.download = "QR-{{ $cliente->nombres }}.png";
            link.href = canvas.toDataURL("image/png", 1.0); // Máxima calidad
            link.click();
        };
        
        // En caso de error con el logo, solo exportar el QR
        logo.onerror = function() {
            const link = document.createElement('a');
            link.download = "QR-{{ $cliente->nombres }}.png";
            link.href = canvas.toDataURL("image/png", 1.0);
            link.click();
        };
    };
});
</script>

</body>
</html>