<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoPaquete extends Model
{
    use HasFactory;

    protected $table = 'estados_paquetes';

    protected $fillable = [
        'estado',
    ];

    public function paquetes(): HasMany
    {
        return $this->hasMany(Paquete::class, 'estados_paquetes_id');
    }
}


