<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Unidad extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'unidades';
    protected $primaryKey = 'id_unidad';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_sede',
        'nombre_unidad',
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'id_sede', 'id_sede');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'id_unidad', 'id_unidad');
    }
}
