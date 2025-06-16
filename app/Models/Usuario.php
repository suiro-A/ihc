<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
  protected $table = 'usuario';

  protected $primaryKey = 'id_usuario';
  
  protected $fillable = [
    'nombres',
    'apellidos',
    'telefono',
    'correo',
    'clave',
    'rol',
    'estado',
  ];
  
  protected $hidden = [
    'clave',
  ];
  
    const ROL_DOCTOR = 1;
    const ROL_RECEPCIONISTA = 2;
    const ROL_ADMIN = 3;
  
  public function isDoctor(): bool
  {
    return $this->rol == self::ROL_DOCTOR;
  }

  public function isRecepcionista(): bool
  {
    return $this->rol == self::ROL_RECEPCIONISTA;
  }

  public function isAdministrativo(): bool
  {
    return $this->rol == self::ROL_ADMIN;
  }

  public function rolNombre()
  {
    return $this->belongsTo(Rol::class, 'rol', 'id_rol');
  }
}
