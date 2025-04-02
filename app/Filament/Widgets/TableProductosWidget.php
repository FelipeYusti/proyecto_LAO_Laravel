<?php

namespace App\Filament\Widgets;

use App\Models\Venta;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TableProductosWidget extends ChartWidget
{
    protected static ?string $heading = 'Chart';
 


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

            'labels' => [
                'Soda de maracuya',
                'Soda de LImon',
                'Soda de Sandi'
            ],
            'datasets' => [
                [
                    'Label' => 'lo que sea niño',
                    'data' =>  [600, 356, 250], // $data->map(fn(TrendValue $venta) => $venta->aggregate), // recorremos el resultado del modelo con un map y una funcion anonima.
                    'backgroundColor' =>  [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)'
                    ]
                ]
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
