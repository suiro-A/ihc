<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alergia extends Model
{
    protected $table = 'alergia';
    protected $primaryKey = 'id_alergia';
    protected $fillable = ['descripcion'];
}
