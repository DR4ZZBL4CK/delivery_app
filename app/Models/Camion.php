<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Camion extends Model
{
    use HasFactory;

    protected $table = 'camiones';

    protected $fillable = [
        'placa',
        'modelo',
    ];

    public function camioneros(): BelongsToMany
    {
        return $this->belongsToMany(Camionero::class, 'camioneros_camiones', 'camiones_id', 'camioneros_id')
            ->withTimestamps();
    }
}


