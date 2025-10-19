<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallePaquete extends Model
{
    use HasFactory;

    protected $table = 'detalles_paquetes';

    protected $fillable = [
        'paquetes_id',
        'tipo_mercancia_id',
        'dimencion',
        'peso',
        'fecha_entrega',
    ];

    public function paquete(): BelongsTo
    {
        return $this->belongsTo(Paquete::class, 'paquetes_id');
    }

    public function tipoMercancia(): BelongsTo
    {
        return $this->belongsTo(TipoMercancia::class, 'tipo_mercancia_id');
    }
}


