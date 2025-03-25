<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Venta extends Model
{

    protected $fillable = ['cliente_id', 'total', 'fecha'];

    protected $table = 'ventas';

    public function detalles()
    {
        return $this->hasMany(Producto_venta::class,'venta_id');
    }
    public function cliente()
    {
        return $this->belongsTo(Cliente::class,'cliente_id');
    }
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_has_venta','venta_id','producto_id')
            ->using(Producto_venta::class)
            ->withPivot('cantidad', 'precio_unit')
            ->withTimestamps();
    }
}
