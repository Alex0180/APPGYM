<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Carbon\Carbon;

class AccesoController extends Controller
{
    public function verificar(Request $request)
    {
        $descriptor = $request->input('descriptor');
        
        if (!$descriptor || !is_array($descriptor)) {
            return response()->json([
                'acceso' => false,
                'mensaje' => 'No se recibió descriptor facial válido.'
            ]);
        }
        
        $acceso = false;
        $mensaje = 'Rostro no reconocido.';
        
        // Obtener todos los clientes con descriptor facial
        $clientes = Cliente::whereNotNull('descriptor_facial')->get();
        
        $mejorSimilitud = 0;
        $clienteEncontrado = null;
        
        foreach($clientes as $cliente) {
            // Obtener descriptor almacenado
            $descriptorAlmacenado = json_decode($cliente->descriptor_facial, true);
            
            if (is_array($descriptorAlmacenado)) {
                // Calcular similitud entre descriptores (distancia euclidiana)
                $similitud = $this->calcularSimilitud($descriptor, $descriptorAlmacenado);
                
                // Encontrar la mayor similitud
                if ($similitud > $mejorSimilitud) {
                    $mejorSimilitud = $similitud;
                    $clienteEncontrado = $cliente;
                }
            }
        }
        
        // Si la similitud es mayor a un umbral (ej. 0.6)
        if ($mejorSimilitud > 0.6 && $clienteEncontrado) {
            // Validar membresía
            $fechaFin = $clienteEncontrado->fecha_fin ?? null;
            
            if($fechaFin && Carbon::now()->lte(Carbon::parse($fechaFin))) {
                $acceso = true;
                $mensaje = "Bienvenido {$clienteEncontrado->nombres}, su acceso ha sido autorizado.";
                
                // Registrar el acceso
                $this->registrarAcceso($clienteEncontrado->id, true);
            } else {
                $mensaje = "Su membresía está vencida. No puede ingresar.";
                
                // Registrar intento de acceso denegado
                $this->registrarAcceso($clienteEncontrado->id, false, 'Membresía vencida');
            }
        }
        
        return response()->json([
            'acceso' => $acceso,
            'mensaje' => $mensaje,
            'similitud' => $mejorSimilitud
        ]);
    }
    
    /**
     * Calcular similitud entre descriptores (distancia euclidiana invertida)
     */
    private function calcularSimilitud($descriptor1, $descriptor2)
    {
        $suma = 0;
        for ($i = 0; $i < count($descriptor1); $i++) {
            $diferencia = $descriptor1[$i] - $descriptor2[$i];
            $suma += $diferencia * $diferencia;
        }
        
        $distancia = sqrt($suma);
        
        // Convertir distancia a similitud (1 = idéntico, 0 =完全不同)
        // La distancia típica entre el mismo rostro es < 0.6, entre rostros diferentes > 0.6
        $similitud = max(0, 1 - $distancia);
        
        return $similitud;
    }
    
    /**
     * Función para registrar descriptores faciales (deberías llamarla al registrar clientes)
     */
    public function registrarDescriptor(Request $request, $clienteId)
    {
        $cliente = Cliente::find($clienteId);
        
        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }
        
        $descriptor = $request->input('descriptor');
        
        if (!$descriptor || !is_array($descriptor)) {
            return response()->json(['error' => 'Descriptor inválido'], 400);
        }
        
        $cliente->descriptor_facial = json_encode($descriptor);
        $cliente->save();
        
        return response()->json(['success' => 'Descriptor facial registrado correctamente']);
    }
    
    /**
     * Registrar intento de acceso
     */
    private function registrarAcceso($clienteId, $exitoso, $razon = null)
    {
        // Aquí implementarías el registro del acceso en tu base de datos
        // Por ejemplo, en una tabla 'accesos'
    }
}