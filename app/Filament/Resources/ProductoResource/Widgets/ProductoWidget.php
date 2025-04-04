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
        $precioPromedio = number_format(Producto::avg('precio'), '1');
        return [
            Stat::make('Productos', Producto::count())
                ->description('Productos Disponibles')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->chart([60, 25, 70, 15, 60, 35, 60])
                ->color('info')
                ->icon('heroicon-o-archive-box'),
            Stat::make('Inventario', Producto::sum('stock'))
                ->description('Productos inventariados')
                ->descriptionIcon('heroicon-m-arrow-trending-down', IconPosition::Before)
                ->chart([15, 50, 10, 45, 25, 60])
                ->color('danger')
                ->icon('heroicon-o-clipboard'),
            Stat::make('Precio Proemdio', $precioPromedio)
                ->description('Promedio del precio de los productos')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->chart([8, 20, 15, 8, 25, 10])
                ->color('success')
                ->icon('heroicon-m-currency-dollar')
        ];
    }
}
