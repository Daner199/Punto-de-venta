<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_venta', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->foreignId('venta_id')->constrained('ventas');         // FK → ventas(id)
            $tabla->foreignId('producto_id')->constrained('productos');   // FK → productos(id)
            $tabla->integer('cantidad');
            $tabla->decimal('precio_cobrado', 10, 2);                     // copia del precio al vender
            $tabla->unique(['venta_id', 'producto_id']);                  // un producto una sola vez por venta
        });

        DB::statement('ALTER TABLE detalle_venta ADD CONSTRAINT cantidad_positiva CHECK (cantidad > 0)');
        DB::statement('ALTER TABLE detalle_venta ADD CONSTRAINT precio_cobrado_positivo CHECK (precio_cobrado > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_venta');
    }
};