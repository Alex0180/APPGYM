<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombres',
        'apellidos',
        'edad',
        'celular',
        'correo',
        'plan',
        'tipo_pago',
        'cantidad',
        'baucher',
        'foto',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $dates = ['fecha_inicio', 'fecha_fin'];

    // ✅ Accesor para calcular los días restantes dinámicamente
    public function getDiasRestantesAttribute()
    {
        if (!$this->fecha_fin) {
            return null;
        }

        $hoy = Carbon::now();
        $fin = Carbon::parse($this->fecha_fin);

        // Si ya venció
        if ($hoy->greaterThan($fin)) {
            return 0;
        }

        return $hoy->diffInDays($fin);
    }
}
