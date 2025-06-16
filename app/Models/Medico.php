<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
  protected $table = 'medico';
  // protected $primaryKey = 'id_usuario';
  protected $primaryKey = 'id_usuario';
  public $incrementing = false;
  protected $fillable = ['id_usuario','especialidad', 'num_colegiatura'];
  public $timestamps = false;

  public function especialidadNombre()
  {
    return $this->belongsTo(Especialidad::class, 'especialidad', 'id_especialidad');
  }
  public function usuario()
  {
      return $this->belongsTo(\App\Models\Usuario::class, 'id_usuario', 'id_usuario');
  }
}
