<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        Producto::create(['nombre' => 'Pan',          'precio_actual' => 1.50]);
        Producto::create(['nombre' => 'Leche',        'precio_actual' => 7]);
        Producto::create(['nombre' => 'Arroz 1kg',    'precio_actual' => 12]);
        Producto::create(['nombre' => 'Aceite 1L',    'precio_actual' => 18.50]);
        Producto::create(['nombre' => 'Coca Cola 2L', 'precio_actual' => 15]);
        Producto::create(['nombre' => 'Huevos x12',   'precio_actual' => 20]);
    }
}