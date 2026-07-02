<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    /** @use HasFactory<\Database\Factories\EmpleadoFactory> */
    use HasFactory;

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'departamento',
        'cargo',
        'estado'
    ];

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class);
    }
}
