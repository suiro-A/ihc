<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialClinico extends Model
{
    protected $table = 'historial_clinico';
    protected $primaryKey = 'id_historial';
    protected $guarded =[    ];


    // * Relacion inversa  uno a uno
    public function pacientes(){

        return $this->belongsTo(Paciente::class, 'id_paciente','id_paciente');
    }

    // * Relacion muchos a muchos
    public function alergias()  {

        return $this->belongsToMany(Alergia::class,'historial_alergia','id_historial','id_alergia','id_historial','id_alergia')->withTimestamps();
        
    }

        public function enfermedades()  {

        return $this->belongsToMany(EnfermedadCronica::class,'historial_enfermedad','id_historial','id_enfermedad','id_historial','id_enfermedad')->withTimestamps();
        
    }

        public function medicamentos()  {

        return $this->belongsToMany(Medicamentos::class,'medicacion_actual','id_historial','id_medicamento','id_historial','id_medicamento')->withTimestamps();
        
    }
}
