<?php

namespace App\Filament\Resources\VentaResource\Widgets;

use App\Models\Venta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VentasWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Cantidad de Ventas', Venta::count())
            ->description('cantidad de ventas realizadas')
        ];
    }
}
