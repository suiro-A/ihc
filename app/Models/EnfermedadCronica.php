<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnfermedadCronica extends Model
{
    protected $table = 'enfermedad_cronica';
    protected $primaryKey = 'id_enfermedad';
    protected $fillable = ['descripcion'];
}
