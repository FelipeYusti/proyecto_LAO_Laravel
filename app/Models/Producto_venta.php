<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

use function Laravel\Prompts\table;

class Producto_venta extends Pivot  // PIVOT :  Esto le dice a Laravel que Producto_venta es un modelo de pivote especial y que no necesita métodos como fromRawAttributes()
{
    protected  $table = 'producto_has_venta';
    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unit',
    ];
   
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
