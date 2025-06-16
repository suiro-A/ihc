<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'paciente';
    protected $primaryKey = 'id_paciente';

    protected $fillable = [
        'nombres',
        'apellidos',
        'dni',
        'fecha_nac',
        'sexo',
        'telefono',
        'correo',
    ];

    protected $casts = [
        'fecha_nac' => 'date',
        'sexo' => 'boolean',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nac' => 'date',
            'sexo' => 'boolean',
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
        return $this->fecha_nac ? \Carbon\Carbon::parse($this->fecha_nac)->age : null;
    }
}
