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
        $totalGanacia = number_format(Venta::sum('total'), '0');

        return [
            Stat::make('Cantidad Ventas', Venta::count())
                ->description(' Ventas realizadas en el año actual')
                ->descriptionIcon('heroicon-o-arrow-trending-up', IconPosition::Before)
                ->chart([1, 2, 5, 10, 15, 35])
                ->color('info')
                ->icon('heroicon-o-shopping-cart'),
            Stat::make(' Total Ventas', $totalGanacia)
                ->description('Suma de todas la ventas')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->chart([1, 2, 5, 10, 15, 35])
                ->color('success')
                ->icon('heroicon-o-currency-dollar')
        ];
    }
}
