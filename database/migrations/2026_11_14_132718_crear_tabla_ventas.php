<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->string('estado', 20)->default('en_curso');   // nace en curso
            $tabla->timestamp('fecha')->useCurrent();            // DEFAULT NOW()
        });

        // Solo se permiten estos tres estados
        DB::statement("ALTER TABLE ventas ADD CONSTRAINT estado_valido CHECK (estado IN ('en_curso', 'finalizada', 'cancelada'))");

        // Solo puede haber UNA venta en curso a la vez
        DB::statement("CREATE UNIQUE INDEX una_sola_venta_en_curso ON ventas (estado) WHERE estado = 'en_curso'");
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};