<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    /** @use HasFactory<\Database\Factories\ActivoFactory> */
    use HasFactory;

    protected $fillable = [
        'codigo_qr',
        'nombre',
        'categoria',
        'marca',
        'modelo',
        'numero_serie',
        'estado',
        'precio',
        'fecha_compra',
        'descripcion',
        'imagen'
    ];

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class);
    }

    public function prestamoActivo()
    {
        return $this->hasOne(Prestamo::class)->where('estado', 'Activo');
    }

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class);
    }

    protected function casts(): array
    {
        return [
            'fecha_compra' => 'date',
            'precio' => 'decimal:2',
        ];
    }
}
