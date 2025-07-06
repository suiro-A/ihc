<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'cita'; // nombre correcto de la tabla
    protected $primaryKey = 'id_cita'; // si tu PK es id_cita

    protected $fillable = [
        'id_historial',
        'id_medico',
        'id_especialidad',
        'motivo',
        'estado',
        'id_hora',
        'fecha',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function historial()
    {
        return $this->belongsTo(HistorialClinico::class, 'id_historial');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_usuario');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad');
    }

    public function horaConsulta()
    {
        return $this->belongsTo(HoraConsulta::class, 'id_hora');
    }

    public function diagnostico()
    {
        return $this->hasOne(Diagnostico::class, 'id_cita', 'id_cita');
    }

    public function indicaciones()
    {
        return $this->hasOne(Indicaciones::class, 'id_cita', 'id_cita');
    }
    
    public function apuntes()
    {
        return $this->hasOne(Apuntes::class, 'id_cita', 'id_cita');
    }
}
