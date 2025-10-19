<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paquete extends Model
{
    use HasFactory;

    protected $table = 'paquetes';

    protected $fillable = [
        'camioneros_id',
        'estados_paquetes_id',
        'direccion',
    ];

    public function camionero(): BelongsTo
    {
        return $this->belongsTo(Camionero::class, 'camioneros_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoPaquete::class, 'estados_paquetes_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePaquete::class, 'paquetes_id');
    }
}


