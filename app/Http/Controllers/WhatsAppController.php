<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\File;

class WhatsAppController extends Controller
{
    // Mostrar vista con QR y enlace de WhatsApp
    public function enviar($id)
    {
        $cliente = Cliente::findOrFail($id);

        // Calcular fechas
        $fechaInicio = $cliente->fecha_inicio ? Carbon::parse($cliente->fecha_inicio) : Carbon::parse($cliente->created_at);
        $diasPlan = match($cliente->plan) {
            'dia' => 1,
            'semana' => 7,
            'quincena' => 15,
            'mes' => 30,
            default => 1,
        };
        $fechaFin = $fechaInicio->copy()->addDays($diasPlan);
        $diasRestantes = Carbon::now()->diffInDays($fechaFin, false);

        // Mensaje extra
        $extra = '';
        if ($diasRestantes === 2) $extra = "⚠️ Te faltan 2 días para terminar tu plan.";
        elseif ($diasRestantes === 1) $extra = "⚠️ Te falta 1 día para terminar tu plan.";
        elseif ($diasRestantes <= 0) $extra = "❌ Tu plan ha expirado. Por favor, renueva tu membresía.";

        // Generar QR
        $qrFileName = $this->generarQR($cliente);
        $qrUrl = asset('storage/qrcodes/' . $qrFileName);

        // Mensaje WhatsApp
        $mensaje = "¡Hola {$cliente->nombres}! 👋\n\n" .
                   "Tu membresía en *" . config('app.name', 'FitZone Gym') . "* está activa. 🏋️‍♂️\n\n" .
                   "📋 *DETALLES DE TU PLAN:*\n" .
                   "• *Plan:* " . ucfirst($cliente->plan) . "\n" .
                   "• *Fecha de inicio:* {$fechaInicio->format('d/m/Y')}\n" .
                   "• *Fecha de vencimiento:* {$fechaFin->format('d/m/Y')}\n" .
                   "• *Días restantes:* " . ($diasRestantes > 0 ? $diasRestantes : 0) . " días\n\n" .
                   $extra . "\n\n" .
                   "📍 Presenta este código QR al ingresar al gimnasio.\n" .
                   "Tu QR: {$qrUrl}\n" .
                   "¡Gracias por confiar en nosotros! 💪";

        // Teléfono limpio
        $telefono = preg_replace('/[^0-9]/', '', $cliente->celular);
        if (strlen($telefono) <= 8) $telefono = '505' . $telefono;

        $urlWhatsApp = "https://wa.me/{$telefono}?text=" . urlencode($mensaje);

        return view('clients.whatsapp', compact(
            'cliente', 'qrFileName', 'mensaje', 'urlWhatsApp', 'fechaFin', 'diasRestantes', 'qrUrl'
        ));
    }

    // Descargar el QR
    public function descargar($id)
    {
        $cliente = Cliente::findOrFail($id);

        $mensaje = "Hola {$cliente->nombre}, tu plan {$cliente->plan} vence el {$cliente->fecha_fin}.";

        $qrPng = QrCode::format('png')
            ->size(800) // aún más grande para descarga
            ->margin(2)
            ->errorCorrection('H')
            ->generate($mensaje);

        $filename = "qr_cliente_{$cliente->id}.png";

        return response($qrPng)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    // Método para generar QR con logo + texto debajo
    private function generarQR($cliente)
    {
        $fechaInicio = $cliente->fecha_inicio ? Carbon::parse($cliente->fecha_inicio) : Carbon::parse($cliente->created_at);
        $diasPlan = match($cliente->plan) {
            'dia' => 1,
            'semana' => 7,
            'quincena' => 15,
            'mes' => 30,
            default => 1,
        };
        $fechaFin = $fechaInicio->copy()->addDays($diasPlan);
        $diasRestantes = Carbon::now()->diffInDays($fechaFin, false);

        $contenidoQR = "CLIENTE: {$cliente->nombres} {$cliente->apellidos}\n" .
                       "GIMNASIO: " . config('app.name', 'FitZone Gym') . "\n" .
                       "PLAN: " . strtoupper($cliente->plan) . "\n" .
                       "FECHA INICIO: {$fechaInicio->format('d/m/Y')}\n" .
                       "FECHA FIN: {$fechaFin->format('d/m/Y')}\n" .
                       "ESTADO: " . ($diasRestantes > 0 ? "ACTIVO" : "VENCIDO");

        $qrDirectory = public_path('storage/qrcodes/');
        if (!file_exists($qrDirectory)) {
            mkdir($qrDirectory, 0755, true);
        }

        $qrFileName = 'qr_' . $cliente->id . '_' . time() . '.png';
        $qrPath = $qrDirectory . $qrFileName;

        try {
            $qrContent = QrCode::format('png')
                ->size(300)
                ->margin(2)
                ->backgroundColor(255, 255, 255)
                ->color(0, 0, 0)
                ->generate($contenidoQR);

            file_put_contents($qrPath, $qrContent);

            // --- Agregar logo ---
            $logoPath = public_path('logo.png');
            if (file_exists($logoPath)) {
                $qrImg = imagecreatefrompng($qrPath);
                $logo = imagecreatefrompng($logoPath);

                $qrWidth = imagesx($qrImg);
                $qrHeight = imagesy($qrImg);
                $logoWidth = imagesx($logo);
                $logoHeight = imagesy($logo);

                $logoTargetWidth = $qrWidth * 0.2;
                $scale = $logoTargetWidth / $logoWidth;
                $logoTargetHeight = $logoHeight * $scale;

                $x = ($qrWidth - $logoTargetWidth) / 2;
                $y = ($qrHeight - $logoTargetHeight) / 2;

                imagecopyresampled(
                    $qrImg, $logo,
                    $x, $y, 0, 0,
                    $logoTargetWidth, $logoTargetHeight,
                    $logoWidth, $logoHeight
                );

                imagepng($qrImg, $qrPath);
                imagedestroy($logo);
                imagedestroy($qrImg);
            }

            // --- Agregar texto debajo ---
            $qrImg = imagecreatefrompng($qrPath);
            $qrWidth = imagesx($qrImg);
            $qrHeight = imagesy($qrImg);
            $extraHeight = 50;
            $finalHeight = $qrHeight + $extraHeight;

            $finalImg = imagecreatetruecolor($qrWidth, $finalHeight);
            $white = imagecolorallocate($finalImg, 255, 255, 255);
            $black = imagecolorallocate($finalImg, 0, 0, 0);

            imagefilledrectangle($finalImg, 0, 0, $qrWidth, $finalHeight, $white);
            imagecopy($finalImg, $qrImg, 0, 0, 0, 0, $qrWidth, $qrHeight);

            $texto = $cliente->nombres . ' - ' . config('app.name', 'FitZone Gym');
            $fontSize = 4;
            $textWidth = imagefontwidth($fontSize) * strlen($texto);
            $x = ($qrWidth - $textWidth) / 2;
            $y = $qrHeight + 15;

            imagestring($finalImg, $fontSize, $x, $y, $texto, $black);

            imagepng($finalImg, $qrPath);
            imagedestroy($qrImg);
            imagedestroy($finalImg);

        } catch (\Exception $e) {
            try {
                $this->generarQRAlternativo($contenidoQR, $qrPath);
            } catch (\Exception $e) {
                $this->crearQRBasico($contenidoQR, $qrPath);
            }
        }

        return $qrFileName;
    }

    // Método alternativo
    private function generarQRAlternativo($contenido, $path)
    {
        $qrCode = QrCode::size(300)
            ->margin(2)
            ->generate($contenido);

        if (is_string($qrCode)) {
            file_put_contents($path, $qrCode);
        } else {
            throw new \Exception('No se pudo generar el QR');
        }
    }

    // Método de respaldo
    private function crearQRBasico($texto, $path)
    {
        $width = 300;
        $height = 300;

        $image = imagecreate($width, $height);
        $background = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);

        imagefilledrectangle($image, 0, 0, $width, $height, $background);

        $textoLimitado = substr($texto, 0, 40);
        $lines = explode("\n", wordwrap($textoLimitado, 20));

        $y = 50;
        foreach ($lines as $line) {
            imagestring($image, 3, 20, $y, $line, $textColor);
            $y += 20;
        }

        imagestring($image, 3, 20, $y + 10, 'Código QR', $textColor);

        imagepng($image, $path);
        imagedestroy($image);
    }

    // Método opcional: enviar WhatsApp directo con QR
    public function enviarConQR($id)
    {
        $cliente = Cliente::findOrFail($id);
        $qrFileName = $this->generarQR($cliente);
        $qrUrl = asset('storage/qrcodes/' . $qrFileName);

        $mensaje = "¡Hola {$cliente->nombres}! 👋\n\n" .
                   "Tu membresía en *" . config('app.name', 'FitZone Gym') . "* está activa. 🏋️‍♂️\n\n" .
                   "📋 *DETALLES DE TU PLAN:*\n" .
                   "• *Plan:* " . ucfirst($cliente->plan) . "\n" .
                   "• *Fecha de vencimiento:* " . ($cliente->fecha_fin ? Carbon::parse($cliente->fecha_fin)->format('d/m/Y') : 'N/A') . "\n" .
                   "📍 Presenta este código QR al ingresar al gimnasio\n\n" .
                   "¡Gracias por confiar en nosotros! 💪\n\n" .
                   "Código QR: {$qrUrl}";

        $telefono = preg_replace('/[^0-9]/', '', $cliente->celular);
        if (strlen($telefono) <= 8) $telefono = '505' . $telefono;

        $urlWhatsApp = "https://wa.me/{$telefono}?text=" . urlencode($mensaje);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'url' => $urlWhatsApp,
                'qr_url' => $qrUrl
            ]);
        }

        return redirect($urlWhatsApp);
    }
}
