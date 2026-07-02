<?php

namespace Database\Factories;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Empleado>
 */
class EmpleadoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->phoneNumber(),
            'departamento' => $this->faker->randomElement(['TI', 'Recursos Humanos', 'Ventas', 'Finanzas', 'Marketing', 'Operaciones']),
            'cargo' => $this->faker->randomElement(['Analista', 'Coordinador', 'Gerente', 'Director', 'Especialista', 'Asistente']),
            'estado' => $this->faker->randomElement(['Activo', 'Inactivo']),
        ];
    }
}
