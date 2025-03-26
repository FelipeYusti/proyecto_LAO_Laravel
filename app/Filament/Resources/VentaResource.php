<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentaResource\Pages;
use App\Filament\Resources\VentaResource\RelationManagers;
use App\Models\Venta;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\DB;
use App\Filament\Resources\Log;
use App\Filament\Resources\VentaResource\Widgets\VentasWidget;
use Dom\Text;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Illuminate\Log\log;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;
    protected static ?string $navigationGroup = 'Tienda';
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    public static function getHeaderWidgets(): array
    {
        return [

            VentaResource\Widgets\VentasWidget::class,
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('nombre')
                            ->required()
                            ->maxWidth(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxWidth(255),
                        TextInput::make('telefono')
                            ->numeric()
                            ->maxWidth(width: 11)
                            ->required()
                    ]),
                Forms\Components\DatePicker::make('fecha')
                    ->default(now()) // Fecha automática
                    ->required(),
                TextInput::make('total')
                    ->label('Total')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated()
                    ->suffix('$')
                    ->afterStateHydrated(function ($component, $state) {
                        // Formatear el valor al cargar el estado
                        $component->state(number_format($state, 2));
                    }),
                Repeater::make('detalles')
                    ->label('Productos')
                    ->relationship('detalles')
                    ->schema([
                        Forms\Components\Select::make('producto_id')
                            ->label('Producto')
                            ->options(Producto::pluck('nombre', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            // actualizamos precios automaticamente
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Obtener el precio del producto seleccionado
                                $producto = Producto::find($state);
                                if ($producto) {
                                    $set('precio_unit', $producto->precio);
                                }
                            }),
                        TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->reactive(),
                        Forms\Components\TextInput::make('precio_unit')
                            ->label('Precio Unitario')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->reactive(),
                    ])

                    ->afterStateUpdated(function ($state, $set) {
                        // Calcular el total
                        $total = collect($state)->sum(function ($item) {
                            return $item['cantidad'] * $item['precio_unit'];
                        });

                        $set('total', $total ? number_format($total, 0, '.', '') : 0); // Actualizar el campo total
                    })
                    ->reactive()
                    ->columns(3)
                    ->collapsible()
                    ->defaultItems(1)
            ],);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cliente.nombre')
                    ->label('Clientes')
                    ->searchable(),
                TextColumn::make('fecha')->date()->label('Fecha de Venta')
                    ->sortable(),
                TextColumn::make('total')
                    ->sortable()
                    ->label('Total de la Venta')
                    ->money('COP')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn($record) => '$' . number_format($record->total, 2)),
                TextColumn::make('detalles')
                    ->label('Resumen de Compra')
                    ->getStateUsing(fn($record) => $record->productos->count() > 0
                        ? $record->productos
                        ->map(fn($p) => "{$p->nombre} ({$p->pivot->cantidad})")
                        ->join(', ')
                        : 'Sin productos')
                    ->tooltip(fn($record) => $record->productos->count() > 0
                        ? $record->productos
                        ->map(fn($p) => "{$p->nombre}: {$p->pivot->cantidad} unidades")
                        ->join("\n")
                        : 'No hay productos en esta venta')
                    ->limit(30),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            RelationManagers\ProductosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'create' => Pages\CreateVenta::route('/create'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }
}
