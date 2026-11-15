<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    // Se ejecuta con "php artisan migrate": crea la tabla
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $tabla) {
            $tabla->id();                                   // SERIAL PRIMARY KEY
            $tabla->string('nombre', 100)->unique();        // VARCHAR(100) NOT NULL UNIQUE
            $tabla->decimal('precio_actual', 10, 2);        // NUMERIC(10,2) NOT NULL
        });

        // Laravel no tiene CHECK, así que lo agregamos en SQL directo
        DB::statement('ALTER TABLE productos ADD CONSTRAINT precio_actual_positivo CHECK (precio_actual > 0)');
    }

    // Se ejecuta con "php artisan migrate:rollback": deshace lo de arriba
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};