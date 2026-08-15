<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $table = 'users';

    protected $primaryKey = 'id_usuario';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_rol',
        'id_sede',
        'area_servicio',
        'nombre',
        'apellido',
        'correo',
        'password',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function getEmailForPasswordReset(): string
    {
        return $this->correo;
    }

    public function getAuthIdentifierName(): string
    {
        return 'id_usuario';
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'id_sede', 'id_sede');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'id_usuario_solicitante', 'id_usuario');
    }

    public function asignacionesTecnico()
    {
        return $this->hasMany(Asignacion::class, 'id_usuario_tecnico', 'id_usuario');
    }

    public function asignacionesCoordinador()
    {
        return $this->hasMany(Asignacion::class, 'id_usuario_coordinador', 'id_usuario');
    }

    public function tieneRol(string $nombreRol): bool
    {
        return $this->rol && $this->rol->nombre_rol === $nombreRol;
    }

    /**
     * Enviar notificacion de restablecimiento de contraseña.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\RestablecerPasswordNotification($token));
    }
}