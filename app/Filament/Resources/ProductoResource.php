<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Widgets\VentasWidget;
use App\Filament\Resources\ProductoResource\Pages;
use App\Filament\Resources\ProductoResource\RelationManagers;
use App\Filament\Resources\VentaResource\Widgets\ProductoWidget;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;
    protected static ?string $navigationGroup = 'Inventario';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    public static string $resource = VentaResource::class;

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
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255) // configuramos la longitud de datos que puede ingresar en el input.
                ,
                Forms\Components\TextInput::make('precio')
                    ->numeric()
                    ->required()
                    ->prefix('$')
                    ->maxValue(42949672.95),
                Forms\Components\TextInput::make('stock')
                    ->numeric()
                    ->required(),
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
                ImageColumn::make('imagen')
                    ->label('Foto')
                    ->size('50px') // Tamañano de la imagen pix
                    ->circular() // hace que la imagen se vea cirular
                ,
                Tables\Columns\TextColumn::make('nombre') // referenciamos el nombre del campo de la base de datos.
                    ->label('Prductos') // Le asignamos un nombre a la columna de 
                    ->searchable(),
                Tables\Columns\TextColumn::make('precio')
                    ->badge()
                    ->color('info')
                    ->money('COP'),
                Tables\Columns\TextColumn::make('stock')
                    ->badge()

                    ->color(fn($record) => $record->stock <= 5 ? 'danger' : ($record->stock <= 15 ? 'warning' : 'success'))

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
