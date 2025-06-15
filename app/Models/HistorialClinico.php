<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialClinico extends Model
{
    use HasFactory;

    protected $table = 'historial_clinico';
    protected $primaryKey = 'id_historial';
    protected $fillable = ['id_paciente'];

    protected function casts(): array
    {
        return [
            'fecha_consulta' => 'datetime',
            'receta_medica' => 'array',
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

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }
}
