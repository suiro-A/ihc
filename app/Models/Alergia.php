<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alergia extends Model
{
    protected $table = 'alergia';
    protected $primaryKey = 'id_alergia';
    protected $guarded =[    ];


    // * Relacion muchos a muchos
    public function  historial()  {

        return $this->belongsToMany(HistorialClinico::class,'historial_alergia','id_alergia','id_historial','id_alergia','id_historial')->withTimestamps();
        
    }
}
