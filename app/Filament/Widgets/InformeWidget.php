<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class InformeWidget extends ChartWidget
{
    protected static ?string $heading = ' Ventas por Mes';
    protected static string $color = 'info';
    protected static ?string $pollingInterval = '10s'; // actualizamos los datos cada 10 seegundo, para tener datos en tiempo real
    protected function getData(): array
    {

        $data = Trend::model(Venta::class)
            ->between(

                start: now()->startOfYear(),
                end: now()->endOfYear()
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'Label' => 'lo que sea niño',
                    'data' =>  $data->map(fn(TrendValue $venta) => $venta->aggregate), // recorremos el resultado del modelo con un map y una funcion anonima
                    'borderColor' => 'rgb(29, 139, 230)',
                    'fill' =>  [
                        'target' => 'origin',
                        'above' => 'rgba(118, 182, 235, 0.38)',
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
