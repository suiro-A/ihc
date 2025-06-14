<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisponibilidadMedica extends Model
{
    use HasFactory;

    protected $table = 'disponibilidad_medica';

    protected $fillable = [
        'doctor_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'disponible',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'hora_inicio' => 'datetime:H:i',
            'hora_fin' => 'datetime:H:i',
            'disponible' => 'boolean',
        ];
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
