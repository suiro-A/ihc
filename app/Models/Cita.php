<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'doctor_id',
        'fecha',
        'hora',
        'motivo',
        'estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'hora' => 'datetime:H:i',
        ];
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function historialClinico()
    {
        return $this->hasOne(HistorialClinico::class);
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha', today());
    }

    public function scopeProximas($query)
    {
        return $query->where('fecha', '>=', today())
                    ->where('estado', 'agendada')
                    ->orderBy('fecha')
                    ->orderBy('hora');
    }
}
