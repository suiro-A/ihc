<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnfermedadCronica extends Model
{
    protected $table = 'enfermedad_cronica';
    protected $primaryKey = 'id_enfermedad';
    protected $guarded =[    ];

    // * Relacion muchos a muchos
    public function  historial()  {

        return $this->belongsToMany(HistorialClinico::class,'historial_enfermedad','id_enfermedad','id_historial','id_enfermedad','id_historial')->withTimestamps();
        
    }
}
