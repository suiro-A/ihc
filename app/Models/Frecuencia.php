<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Frecuencia extends Model
{
    protected $table = 'frecuencia';
    protected $primaryKey = 'id_frecuencia';
    public $timestamps = false;
    
    protected $fillable = ['descripcion'];
    
    public function recetaMedicamentos()
    {
        return $this->hasMany(RecetaMedicamento::class, 'id_frecuencia', 'id_frecuencia');
    }
}
