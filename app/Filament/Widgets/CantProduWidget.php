<?php

namespace App\Filament\Widgets;

use App\Models\Producto_venta;
use App\Models\Venta;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class CantProduWidget extends ChartWidget
{
    protected static ?string $heading = 'Productos Vendidos';
    protected int|string|array $columnSpan = 1;
    protected static ?string $pollingInterval = '10s'; // actualizamos los datos cada 10 seegundo, para tener datos en tiempo real
    protected function getData(): array
    {
        $data = Trend::model(Producto_venta::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear()
            )
            ->perMonth()
            ->sum('cantidad');

        return [
            'datasets' => [
                [
                    'label' => 'Productos',
                    'data' =>  $data->map(fn(TrendValue $venta) => $venta->aggregate),
                    'borderColor' => 'rgb(250, 124, 21)',
                    'fill' =>  [
                        'target' => 'origin',
                        'above' => 'rgba(216, 141, 28, 0.28)',
                        'below' => 'rgb(250, 133, 24)'
                    ]
                ]
            ],
            'labels' => $data->map(fn(TrendValue $venta) => Carbon::parse($venta->date)->format('M')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
