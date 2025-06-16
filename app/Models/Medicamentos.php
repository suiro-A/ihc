<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamentos extends Model
{
    protected $table = 'medicamento';
    protected $primaryKey = 'id_medicamento';
    protected $guarded =[    ];

    // * Relacion muchos a muchos
    public function  historial()  {

        return $this->belongsToMany(HistorialClinico::class,'medicacion_actual','id_medicamento','id_historial','id_medicamento','id_historial')->withTimestamps();
        
    }
}
