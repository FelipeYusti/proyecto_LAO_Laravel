<?php

namespace App\Filament\Resources\VentaResource\Widgets;

use App\Models\Venta;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VentasWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Cantidad Ventas', Venta::count())
                ->description(' Ventas realizadas en el año actual')
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
