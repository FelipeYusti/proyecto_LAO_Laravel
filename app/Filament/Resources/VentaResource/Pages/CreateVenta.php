<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Models\Producto;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;
    protected function afterCreate()
    {
        // Obtener el ID del producto y la cantidad vendida
        $detalles = $this->data['detalles'];
        foreach ($detalles as $val) {
            echo 'id producto:' . $val['producto_id'];
            echo 'cantidad:' .  $val['cantidad'];
        };
        $productoId = $detalles['producto_id'];
        $cantidadVendida = $detalles['cantidad'];

        // Actualizar el stock
        $this->actualizarStock($productoId, $cantidadVendida);
    }

    private function actualizarStock($productoId, $cantidadVendida)
    {
        // Lógica para actualizar el stock (similar al paso 2)
        $producto = Producto::find($productoId);
        $nuevoStock = $producto->stock - $cantidadVendida;

        // Actualizar el stock
        $producto->update(['stock' => $nuevoStock]);
    }
}
