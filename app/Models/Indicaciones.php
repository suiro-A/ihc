<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indicaciones extends Model
{
    protected $table = 'indicaciones';
    protected $primaryKey = 'id_indicacion';

    protected $fillable = [
        'id_cita',
        'descripcion',
    ];

    /**
     * Relación con la cita
     */
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }
}
