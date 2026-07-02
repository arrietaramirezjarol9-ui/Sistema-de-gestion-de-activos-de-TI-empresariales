<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Empleado;
use App\Models\Activo;
use App\Models\Prestamo;
use App\Models\Mantenimiento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario Administrador por defecto
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Crear usuario Técnico por defecto
        User::create([
            'name' => 'Técnico de Soporte',
            'email' => 'tecnico@admin.com',
            'password' => Hash::make('password'),
            'role' => 'tecnico',
        ]);

        // Crear Empleados
        $empleados = Empleado::factory(25)->create();

        // Crear Activos
        $activos = Activo::factory(50)->create();

        // Para cada activo, aseguramos la consistencia de su estado en prestamos o mantenimientos
        foreach ($activos as $activo) {
            $fechaCompra = Carbon::parse($activo->fecha_compra);

            if ($activo->estado === 'Asignado') {
                // Si el activo está asignado, creamos un préstamo activo para un empleado aleatorio
                Prestamo::create([
                    'activo_id' => $activo->id,
                    'empleado_id' => $empleados->random()->id,
                    'fecha_prestamo' => $fechaCompra->copy()->addDays(rand(1, 30)),
                    'fecha_devolucion' => null,
                    'estado' => 'Activo',
                    'observaciones' => 'Entrega inicial de equipo de trabajo.',
                ]);
            } elseif ($activo->estado === 'Mantenimiento') {
                // Si está en mantenimiento, creamos un registro de mantenimiento activo
                Mantenimiento::create([
                    'activo_id' => $activo->id,
                    'fecha_inicio' => Carbon::now()->subDays(rand(1, 10)),
                    'fecha_fin' => null,
                    'costo' => rand(50, 300),
                    'descripcion' => 'Mantenimiento preventivo general y limpieza de componentes.',
                    'estado' => fake()->randomElement(['Programado', 'En Proceso']),
                ]);
            }
            
            // Adicionalmente, creamos un historial de préstamos devueltos para algunos equipos aleatorios
            if (rand(0, 1) === 1) {
                // Crear un préstamo ya devuelto en el pasado
                $fechaInicio = $fechaCompra->copy()->addDays(rand(1, 10));
                $fechaFin = $fechaInicio->copy()->addDays(rand(10, 60));
                
                // Solo si la fecha de fin es menor que hoy
                if ($fechaFin->lt(Carbon::now())) {
                    Prestamo::create([
                        'activo_id' => $activo->id,
                        'empleado_id' => $empleados->random()->id,
                        'fecha_prestamo' => $fechaInicio,
                        'fecha_devolucion' => $fechaFin,
                        'estado' => 'Devuelto',
                        'observaciones' => 'Devolución de equipo al término del proyecto/cambio de rol.',
                    ]);
                }
            }
        }
    }
}
