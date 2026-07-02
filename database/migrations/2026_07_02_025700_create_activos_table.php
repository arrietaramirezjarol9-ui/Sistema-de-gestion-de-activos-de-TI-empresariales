<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_qr')->unique();
            $table->string('nombre');
            $table->enum('categoria', ['Laptop', 'PC', 'Impresora', 'Celular', 'Accesorio']);
            $table->string('marca');
            $table->string('modelo');
            $table->string('numero_serie')->unique();
            $table->enum('estado', ['Disponible', 'Asignado', 'Mantenimiento', 'De Baja'])->default('Disponible');
            $table->decimal('precio', 10, 2);
            $table->date('fecha_compra');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activos');
    }
};
