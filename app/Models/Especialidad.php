<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidad';
    protected $primaryKey = 'id_especialidad';

    protected $fillable = [
        'nombre',
    ];

    public function medicos()
    {
        return $this->hasMany(Medico::class, 'especialidad');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_especialidad');
    }
}
