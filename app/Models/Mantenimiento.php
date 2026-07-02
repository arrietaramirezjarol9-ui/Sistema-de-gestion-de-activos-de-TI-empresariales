<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    /** @use HasFactory<\Database\Factories\MantenimientoFactory> */
    use HasFactory;

    protected $fillable = [
        'activo_id',
        'fecha_inicio',
        'fecha_fin',
        'costo',
        'descripcion',
        'estado'
    ];

    public function activo()
    {
        return $this->belongsTo(Activo::class);
    }

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'costo' => 'decimal:2',
        ];
    }
}
