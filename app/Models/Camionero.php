<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Camionero extends Model
{
    use HasFactory;

    protected $table = 'camioneros';

    protected $fillable = [
        'documento',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'licencia',
        'telefono',
    ];

    public function camiones(): BelongsToMany
    {
        return $this->belongsToMany(Camion::class, 'camioneros_camiones', 'camioneros_id', 'camiones_id')
            ->withTimestamps();
    }

    public function paquetes(): HasMany
    {
        return $this->hasMany(Paquete::class, 'camioneros_id');
    }
}


