<?php

namespace App\Models;

use Filament\Support\Concerns\HasFooterActionsAlignment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{

    protected $fillable = ['nombre', 'precio', 'stock', 'imagen']; // fillable: permite la asigncion masiva de datos - especifiacmos los campos que se pueden llenar de forma masiva.
    protected $casts = [ // casts : convierte los datos en el formato correcto.
        'precio' => 'float',
        'stock' => 'integer'
    ];
    public function detalles()
    {
        return $this->hasMany(Producto_venta::class);
    }


    public function ventas(): BelongsToMany
    {
        return $this->belongsToMany(Venta::class, 'producto_has_venta','producto_id','venta_id')
            ->withPivot('cantidad', 'precio_unit')
            ->using(Producto_venta::class)
            ->withTimestamps();
    }
}
