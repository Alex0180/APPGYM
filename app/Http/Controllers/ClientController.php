<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function register()
    {
        return view('clients.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'edad' => 'required|integer',
            'celular' => 'required|string',
            'correo' => 'nullable|email',
            'plan' => 'required|string',
            'tipo_pago' => 'required|string',
            'cantidad' => 'nullable|numeric',
            'baucher' => 'nullable|string',
            'foto' => 'nullable|string',
        ]);

        $fechaInicio = Carbon::parse($request->fecha_inicio);

        // Calcular fecha fin según el plan
        $fechaFin = $this->calcularFechaFin($fechaInicio, $request->plan);

        // Guardar foto en storage si viene en base64
        $fotoPath = null;
        if ($request->foto) {
            $image = str_replace('data:image/png;base64,', '', $request->foto);
            $image = str_replace(' ', '+', $image);
            $imageName = 'cliente_' . time() . '.png';
            Storage::disk('public')->put('clientes/' . $imageName, base64_decode($image));
            $fotoPath = 'clientes/' . $imageName;
        }

        Cliente::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'edad' => $request->edad,
            'celular' => $request->celular,
            'correo' => $request->correo,
            'plan' => $request->plan,
            'tipo_pago' => $request->tipo_pago,
            'cantidad' => $request->tipo_pago === 'efectivo' ? $request->cantidad : null,
            'baucher' => $request->tipo_pago === 'transferencia' ? $request->baucher : null,
            'foto' => $fotoPath,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
        ]);

        return redirect()->route('list_clients')->with('success', 'Cliente registrado correctamente.');
    }

    public function list()
    {
        $clientes = Cliente::all();
        return view('clients.list', compact('clientes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'edad' => 'required|integer',
            'celular' => 'required|string',
            'correo' => 'nullable|email',
            'plan' => 'required|string',
            'tipo_pago' => 'required|string',
            'cantidad' => 'nullable|numeric',
            'baucher' => 'nullable|string',
        ]);

        $cliente = Cliente::findOrFail($id);

        $fechaInicio = Carbon::parse($request->fecha_inicio);
        $fechaFin = $this->calcularFechaFin($fechaInicio, $request->plan);

        $cliente->update([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'edad' => $request->edad,
            'celular' => $request->celular,
            'correo' => $request->correo,
            'plan' => $request->plan,
            'tipo_pago' => $request->tipo_pago,
            'cantidad' => $request->tipo_pago === 'efectivo' ? $request->cantidad : null,
            'baucher' => $request->tipo_pago === 'transferencia' ? $request->baucher : null,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
        ]);

        return redirect()->route('list_clients')->with('success', 'Cliente actualizado correctamente.');
    }

    // Función para calcular fecha fin según plan
    private function calcularFechaFin($fechaInicio, $plan)
    {
        switch ($plan) {
            case 'dia':
                return $fechaInicio->copy()->addDay();
            case 'semana':
                return $fechaInicio->copy()->addWeek();
            case 'quincena':
                return $fechaInicio->copy()->addDays(15);
            case 'mes':
                return $fechaInicio->copy()->addMonth();
            default:
                return null;
        }
    }

    public function destroy($id)
{
    $cliente = Cliente::findOrFail($id);

    // Opcional: eliminar la foto del storage si existe
    if ($cliente->foto) {
        \Storage::disk('public')->delete($cliente->foto);
    }

    $cliente->delete();

    return redirect()->route('list_clients')->with('success', 'Cliente eliminado correctamente.');
}


public function index(Request $request)
{
    $query = Cliente::query();

    if ($request->filled('nombre')) {
        $query->where('nombres', 'like', '%' . $request->nombre . '%')
              ->orWhere('apellidos', 'like', '%' . $request->nombre . '%');
    }

    if ($request->filled('plan')) {
        $query->where('plan', $request->plan);
    }

    if ($request->filled('tipo_pago')) {
        $query->where('tipo_pago', $request->tipo_pago);
    }

    $clientes = $query->get();

    return view('clientes.index', compact('clientes'));
}

}
