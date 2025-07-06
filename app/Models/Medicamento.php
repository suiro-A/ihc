<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'medicamento';
    protected $primaryKey = 'id_medicamento';
    protected $fillable = ['nombre', 'descripcion', 'presentacion'];
    
    public function recetaMedicamentos()
    {
        return $this->hasMany(RecetaMedicamento::class, 'id_medicamento', 'id_medicamento');
    }
    
    public function recetas()
    {
        return $this->belongsToMany(Receta::class, 'receta_medicamento', 'id_medicamento', 'id_receta')
                   ->withPivot('id_frecuencia', 'dosis', 'duración');
    }
}
