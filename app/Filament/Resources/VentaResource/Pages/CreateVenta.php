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
        $detalles = $this->data['detalles'];

        foreach ($detalles as $val) {

            $this->actualizarStock($val['producto_id'], $val['cantidad']);
        };
    }

    private function actualizarStock($productoId, $cantidadVendida)
    {
        Producto::find($productoId)->decrement('stock', $cantidadVendida);
    }
}
