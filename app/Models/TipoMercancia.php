<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoMercancia extends Model
{
    use HasFactory;

    protected $table = 'tipo_mercancia';

    protected $fillable = [
        'tipo',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePaquete::class, 'tipo_mercancia_id');
    }
}


