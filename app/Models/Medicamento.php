<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'medicamento';
    protected $primaryKey = 'id_medicamento';
    protected $fillable = ['nombre', 'descripcion', 'presentacion'];
}
