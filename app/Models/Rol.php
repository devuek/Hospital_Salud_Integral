<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Rol extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'roles';

    protected $primaryKey = 'id_rol';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nombre_rol',
        'descripcion',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_rol', 'id_rol');
    }
}
