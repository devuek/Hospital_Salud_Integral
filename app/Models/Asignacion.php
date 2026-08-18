<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Asignacion extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'asignaciones';
    protected $primaryKey = 'id_asignacion';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_solicitud',
        'id_usuario_tecnico',
        'id_usuario_coordinador',
        'fecha_asignacion',
        'is_pausada',
        'motivo_pausa',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'is_pausada' => 'boolean',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud', 'id_solicitud');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'id_usuario_tecnico', 'id_usuario');
    }

    public function coordinador()
    {
        return $this->belongsTo(User::class, 'id_usuario_coordinador', 'id_usuario');
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'id_asignacion', 'id_asignacion');
    }
}
