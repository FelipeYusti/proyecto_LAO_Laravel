<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TotalVentaWidget extends ChartWidget
{
    protected static string $color = 'success'; // configuracion de color 
    protected static ?string $pollingInterval = '10s'; // actualizamos los datos cada 10 seegundo, para tener datos en tiempo real

    protected static ?string $heading = 'Total ventas';

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
                    'Label' => 'lo que sea niño',
                    'data' =>  $data->map(fn(TrendValue $venta) => $venta->aggregate), // recorremos el resultado del modelo con un map y una funcion anonima.
                    'borderColor' => 'rgb(17, 226, 34)',
                    'fill' =>  [
                        'target' => 'origin',
                        'above' => 'rgba(128, 235, 146, 0.38)',
                        'below' => 'rgb(0, 0, 255)'
                    ]
                ]
            ],
            'labels' => $data->map(fn(TrendValue $venta) => $venta->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
