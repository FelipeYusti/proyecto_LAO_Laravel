<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use App\Filament\Resources\VentaResource\Widgets\VentasWidget;
use Filament\Actions;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;

class ListVentas extends ListRecords
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-m-plus'),
                
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            VentasWidget::class // Widget en la parte superior
        ];
    }
}
