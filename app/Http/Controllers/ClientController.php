<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Carbon\Carbon;

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

        // Fecha de inicio = hoy
        $fechaInicio = Carbon::now();
        $dias = 0;

        // Asignar días según plan
        switch ($request->plan) {
            case 'dia':
                $dias = 1;
                break;
            case 'semana':
                $dias = 7;
                break;
            case 'quincena':
                $dias = 15;
                break;
            case 'mes':
                $dias = 30;
                break;
        }

        $fechaFin = $fechaInicio->copy()->addDays($dias);

        // Guardar foto en storage
        $fotoPath = null;
        if ($request->foto) {
            $image = str_replace('data:image/png;base64,', '', $request->foto);
            $image = str_replace(' ', '+', $image);
            $imageName = 'cliente_' . time() . '.png';
            \Storage::disk('public')->put('clientes/' . $imageName, base64_decode($image));
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
}
