<?php

namespace Database\Factories;

use App\Models\Activo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activo>
 */
class ActivoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoria = $this->faker->randomElement(['Laptop', 'PC', 'Impresora', 'Celular', 'Accesorio']);
        
        $marcas = [
            'Laptop' => ['Lenovo', 'HP', 'Dell', 'Apple', 'Asus'],
            'PC' => ['Dell', 'HP', 'Lenovo', 'Clon/Gamer'],
            'Impresora' => ['HP', 'Epson', 'Canon', 'Brother'],
            'Celular' => ['Samsung', 'Apple', 'Xiaomi', 'Motorola'],
            'Accesorio' => ['Logitech', 'Razer', 'HyperX', 'Sony', 'Genius']
        ];
        
        $marca = $this->faker->randomElement($marcas[$categoria]);
        
        $modelos = [
            'Laptop' => ['ThinkPad L14', 'EliteBook 840', 'Latitude 5420', 'MacBook Air', 'ZenBook 14'],
            'PC' => ['OptiPlex 7080', 'ProDesk 600', 'ThinkCentre M70q', 'Intel Core i7 16GB'],
            'Impresora' => ['LaserJet Pro M404dn', 'EcoTank L3250', 'Pixma G3110', 'HL-L2350DW'],
            'Celular' => ['Galaxy S23', 'iPhone 14', 'Redmi Note 12', 'Moto G84'],
            'Accesorio' => ['Mouse MX Master 3', 'Teclado K120', 'Audífonos Cloud II', 'Cámara Web C920', 'Teclado Mecánico G413']
        ];
        
        $modelo = $this->faker->randomElement($modelos[$categoria]);
        
        return [
            'codigo_qr' => 'ACT-' . str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'nombre' => $marca . ' ' . $modelo,
            'categoria' => $categoria,
            'marca' => $marca,
            'modelo' => $modelo,
            'numero_serie' => strtoupper($this->faker->unique()->bothify('SN-??##?##?##')),
            'estado' => $this->faker->randomElement(['Disponible', 'Asignado', 'Mantenimiento', 'De Baja']),
            'precio' => $this->faker->randomFloat(2, 50, 2000),
            'fecha_compra' => $this->faker->dateTimeBetween('-3 years', 'now'),
            'descripcion' => $this->faker->sentence(10),
        ];
    }
}
