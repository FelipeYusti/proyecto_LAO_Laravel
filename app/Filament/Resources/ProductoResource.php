<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Filament\Resources\ProductoResource\RelationManagers;
use App\Filament\Resources\ProductoResource\Widgets\ProductoWidget;
use App\Models\Producto;
use Dompdf\Css\Color;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;
    protected static ?string $navigationGroup = 'Inventario';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getWidgets(): array

    {
        return [
            ProductoWidget::class,
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255) // configuramos la longitud de datos que puede ingresar en el input.
                ,
                Forms\Components\Textarea::make('ingredientes')
                    ->label('Ingredientes')
                    ->required()
                    ->maxLength(250),
                Forms\Components\TextInput::make('precio')
                    ->numeric()
                    ->required()
                    ->prefix('$')
                    ->maxValue(42949672.95),
                Forms\Components\TextInput::make('stock')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripcion del Producto')
                    ->required()
                    ->maxLength(250),
                FileUpload::make('imagen')
                    ->label('Imagen del Producto')
                    ->image()
                    ->directory('productos')
                    ->maxSize(1024), // Tamaño máximo en KB (1MB)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    ImageColumn::make('imagen')
                        ->size('100px')
                        ->circular(),
                    Tables\Columns\TextColumn::make('nombre')
                        ->label('Productos')
                        ->tooltip('Nombre del Producto')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('descripcion')
                        ->color('warning'),
                    Tables\Columns\TextColumn::make('precio')
                        ->badge()
                        ->color('info')
                        ->money('COP')
                        ->tooltip('Precio del Producto'),
                    Tables\Columns\TextColumn::make('stock')
                        ->badge()
                        ->color(fn($record) => $record->stock <= 5 ? 'danger' : ($record->stock <= 15 ? 'warning' : 'success'))
                        ->tooltip('Stock Disponible')
                        ->icon(fn($record) => $record->stock <= 5 ? 'heroicon-o-exclamation-circle' : null),
                ])->space(2),   // Espaciado entre los elementos
            ])
            ->contentGrid([
                'md' => 3,
                'xl' => 4
            ])
            ->filters([
                Tables\Filters\Filter::make('LowStock')
                    ->query(fn($query) => $query->where('stock', '<=', 5))
                    ->label('Bajo Stock'),
                Tables\Filters\Filter::make('HighPrice')
                    ->query(fn($query) => $query->where('precio', '>', 5000))
                    ->label('Precio Alto'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal')->color('warning')->tooltip('Acciones')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])

            ->defaultPaginationPageOption(12);   // Paginación de 12 tarjetas por página
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}
