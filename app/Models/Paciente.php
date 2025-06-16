<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;
    
    protected $table = 'paciente';
    protected $primaryKey = 'id_paciente';
    protected $guarded =[    ];


    // * Relacion uno a uno
    public function  historial()  {

        return $this->hasOne(HistorialClinico::class,'id_paciente','id_paciente');
        
    }
}
