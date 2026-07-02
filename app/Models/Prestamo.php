<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    /** @use HasFactory<\Database\Factories\PrestamoFactory> */
    use HasFactory;

    protected $fillable = [
        'activo_id',
        'empleado_id',
        'fecha_prestamo',
        'fecha_devolucion',
        'estado',
        'observaciones'
    ];

    public function activo()
    {
        return $this->belongsTo(Activo::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    protected function casts(): array
    {
        return [
            'fecha_prestamo' => 'date',
            'fecha_devolucion' => 'date',
        ];
    }
}
