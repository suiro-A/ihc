<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoraConsulta extends Model
{
    protected $table = 'hora_consulta';
    protected $primaryKey = 'id_hora';
    
    protected $fillable = [
        'hora_inicio',
        'hora_fin',
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_hora');
    }
}
