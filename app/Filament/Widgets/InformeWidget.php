<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class InformeWidget extends ChartWidget
{
    protected static ?string $heading = ' Ventas por Mes';
    protected int|string|array $columnSpan = 1;
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
                    'label' => 'Ventas',
                    'data' =>  $data->map(fn(TrendValue $venta) => $venta->aggregate), // recorremos el resultado del modelo con un map y una funcion anonima
                    'borderColor' => 'rgb(175, 38, 255)',
                    'fill' =>  [
                        'target' => 'origin',
                        'above' => 'rgba(123, 27, 187, 0.28)',
                        'below' => 'rgb(183, 0, 255)'
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
