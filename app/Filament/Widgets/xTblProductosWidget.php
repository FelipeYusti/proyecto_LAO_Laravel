<?php

namespace App\Filament\Widgets;

use App\Models\Producto_venta;
use App\Models\Venta;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class xTblProductosWidget extends ChartWidget
{
    protected static ?string $heading = 'Total Productos';
    protected int|string|array $columnSpan = 1;

    protected static ?string $pollingInterval = '10s';
    protected function getData(): array
    {
        $productosMasVendidos = Producto_venta::select('producto_id')
            ->selectRaw('SUM(cantidad) as total_vendido')
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->limit(4)
            ->with('producto')
            ->get();
        $labels = $productosMasVendidos->pluck('producto.nombre')->toArray();
        $ventas = $productosMasVendidos->pluck('total_vendido')->toArray();
        return [

            'labels' => $labels,
            'datasets' => [
                [
                    'Label' => 'lo que sea niño',
                    'data' => $ventas,
                    'backgroundColor' =>  [
                        'rgb(231, 202, 37)',
                        'rgb(58, 130, 172)',
                        'rgb(11, 31, 83)',
                        'rgba(133, 140, 143, 0.77)',

                    ],
                    ' hoverOffset' => 4,
                ]
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
