<?php

namespace App\Filament\Resources\ProductoResource\Widgets;

use App\Models\Producto;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductoWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Productos', Producto::count())
                ->description('Cantidad de productos')
                ->chart([60, 25,70, 15, 60, 35, 60])
                ->color('info')
                ->descriptionIcon('heroicon-m-archive-box', IconPosition::Before),
            Stat::make('Invetario', Producto::sum('stock'))
                ->description('Productos inventariados')
                ->descriptionIcon('heroicon-m-clipboard', IconPosition::Before)
                ->chart([15,50 , 10, 45, 25, 60])
                ->color('danger'),
            Stat::make('Precio Proemdio', intval(Producto::avg('precio')))
                ->description('Promedio del precio de los productos')
                ->descriptionIcon('heroicon-m-currency-dollar', IconPosition::Before)
                ->chart([8, 20, 15, 8, 25, 10])
                ->color('success')
        ];
    }
}
