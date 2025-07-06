<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apuntes extends Model
{
    protected $table = 'apuntes';
    protected $primaryKey = 'id_apunte';

    protected $fillable = [
        'id_cita',
        'sintomas_reportados',
        'exploracion_fisica',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }
}
