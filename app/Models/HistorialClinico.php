<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialClinico extends Model
{
    use HasFactory;

    protected $table = 'historial_clinico';
    protected $primaryKey = 'id_historial';

    protected $fillable = [
        'id_paciente',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_historial');
    }
}
