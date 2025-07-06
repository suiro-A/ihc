<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    protected $table = 'receta';
    protected $primaryKey = 'id_receta';

    protected $fillable = [
        'id_cita'
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }
    
    public function recetaMedicamentos()
    {
        return $this->hasMany(RecetaMedicamento::class, 'id_receta', 'id_receta');
    }
    
    public function medicamentos()
    {
        return $this->belongsToMany(Medicamento::class, 'receta_medicamento', 'id_receta', 'id_medicamento')
                   ->withPivot('id_frecuencia', 'dosis', 'duración');
    }
}
