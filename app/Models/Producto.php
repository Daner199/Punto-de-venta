<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    // Laravel buscaría la tabla "productos" solo; lo ponemos explícito para que se lea claro
    protected $table = 'productos';

    // Nuestras tablas no tienen created_at / updated_at
    public $timestamps = false;

    // Columnas que se pueden llenar desde el código
    protected $fillable = ['nombre', 'precio_actual'];
}