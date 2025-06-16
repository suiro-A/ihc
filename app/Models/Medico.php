<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
  protected $table = 'medico';
  // protected $primaryKey = 'id_usuario';
  protected $fillable = ['id_usuario','especialidad', 'num_colegiatura'];
  public $timestamps = false;
}
