<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecetaMedicamento extends Model
{
    protected $table = 'receta_medicamento';
    public $timestamps = false;
    
    // Clave primaria compuesta
    protected $primaryKey = ['id_medicamento', 'id_receta'];
    public $incrementing = false;
    
    protected $fillable = [
        'id_medicamento',
        'id_receta', 
        'id_frecuencia',
        'dosis',
        'duración',
        'instrucciones'
    ];
    
    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'id_medicamento', 'id_medicamento');
    }
    
    public function receta()
    {
        return $this->belongsTo(Receta::class, 'id_receta', 'id_receta');
    }
    
    public function frecuencia()
    {
        return $this->belongsTo(Frecuencia::class, 'id_frecuencia', 'id_frecuencia');
    }
}
