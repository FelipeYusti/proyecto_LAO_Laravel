<?php

namespace App\Filament\Widgets;

use App\Models\Producto_venta;
use App\Models\Venta;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TestWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(' Ventas', Venta::count())
                ->description('Cantidad de ventas realizadas')
                ->descriptionIcon('heroicon-m-building-storefront', IconPosition::Before)
                ->chart([1, 2, 5, 10, 15, 35])
                ->color('info'),
            Stat::make(' Total Ventas', Venta::sum('total'))
                ->description('Suma de todas la ventas')
                ->descriptionIcon('heroicon-m-currency-dollar', IconPosition::Before)
                ->chart([1, 2, 5, 10, 15, 35])
                ->color('success')
        ];
    }
}
