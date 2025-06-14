<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellidos',
        'dni',
        'fecha_nacimiento',
        'genero',
        'telefono',
        'email',
        'direccion',
        'alergias',
        'enfermedades_cronicas',
        'medicacion_actual',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function historialClinico()
    {
        return $this->hasMany(HistorialClinico::class);
    }

    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellidos;
    }

    public function getEdadAttribute()
    {
        return $this->fecha_nacimiento->age;
    }
}
