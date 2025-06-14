<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telefono',
        'especialidad',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'doctor_id');
    }

    public function disponibilidades()
    {
        return $this->hasMany(DisponibilidadMedica::class, 'doctor_id');
    }

    public function isDoctor()
    {
        return $this->hasRole('doctor');
    }

    public function isRecepcionista()
    {
        return $this->hasRole('recepcionista');
    }

    public function isAdministrativo()
    {
        return $this->hasRole('administrativo');
    }
}
