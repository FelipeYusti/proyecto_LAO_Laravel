<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TotalVentaWidget extends ChartWidget
{
    protected static string $color = 'success'; // configuracion de color 
    protected static ?string $pollingInterval = '10s'; // actualizamos los datos cada 10 seegundo, para tener datos en tiempo real
    protected int|string|array $columnSpan = 1;
    protected static ?string $heading = 'Total Ventas';

    protected function getData(): array
    {
        $data = Trend::model(Venta::class)
            ->between(

                start: now()->startOfYear(),
                end: now()->endOfYear()
            )
            ->perMonth()
            ->sum('total');

        return [
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' =>  $data->map(fn(TrendValue $venta) => $venta->aggregate), // recorremos el resultado del modelo con un map y una funcion anonima.
                    'borderColor' => 'rgb(46, 117, 209)',
                    'fill' =>  [
                        'target' => 'origin',
                        'above' => 'rgba(71, 143, 192, 0.27)',
                        'below' => 'rgb(0, 0, 255)'
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
