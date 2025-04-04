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
use App\Filament\Widgets\TestWidget;
use App\Models\Cliente;
use Barryvdh\DomPDF\Facade\Pdf;
use Dom\Text;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function Illuminate\Log\log;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;
    protected static ?string $navigationGroup = 'Tienda';
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function getWidgets(): array
    {
        return [
            VentasWidget::class,
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
                    ->readOnly()
                    ->required(),
                TextInput::make('total')
                    ->label('Total')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated()
                    ->suffixIcon('heroicon-s-currency-dollar')
                    ->suffixIconColor('success')
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
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $producto = Producto::find($state);
                                if ($producto) {
                                    $set('precio_unit', $producto->precio);
                                }
                            }),
                        TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->minValue(1)
                            // validamos que no pueda hacer una venta si la cantiada es mayor al stock.
                            ->maxValue(fn($get) => Producto::find($get('producto_id'))?->stock ?? 0)
                            ->required()
                            ->reactive(),
                        Forms\Components\TextInput::make('precio_unit')
                            ->label('Precio Unitario')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->suffixIcon('heroicon-m-banknotes')
                            ->suffixIconColor('success')
                            ->reactive(),
                    ])
                    ->afterStateUpdated(function ($state, $set) {
                        // Calcular el total
                        $total = collect($state)->sum(function ($item) {;
                            return   intval($item['cantidad'])  *  intval($item['precio_unit']);
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
            ->headerActions([
                Action::make('export_pdf')
                    ->label('PDF')
                    ->color('danger')
                    ->action(function () {
                        $ventas = Venta::all()->map(function ($venta) {
                            $productosInfo = $venta->productos->map(function ($producto) {
                                return "{$producto->nombre} ({$producto->pivot->cantidad})";
                            })->join(', ');
                            $venta->productos_info = $productosInfo;
                            return $venta;
                        });
                        $pdf = Pdf::loadView('exports.venta', ['ventas' => $ventas]);
                        return response()->streamDownload(fn() => print($pdf->output()), 'ventas.pdf');
                    })->icon('heroicon-o-arrow-down-tray')
            ])
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
                    //fn es una forma corta de definir funciones anónimas en PHP,
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
            ])->filters([
                /* Tables\Filters\Filter::make('HighPrice')
                    ->query(fn($query) => $query->where('precio', '>', 5000))
                    ->label('Precio Alto'), */
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                ]),

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
