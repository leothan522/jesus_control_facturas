<?php

namespace App\Filament\Resources\FacturaResource\RelationManagers;

use App\Models\Articulo;
use App\Models\Factura;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('articulos_id')
                    ->label('Articulo')
                    ->relationship('articulo', 'descripcion')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('descripcion')
                            ->unique()
                            ->maxLength(255)
                            ->required(),
                        Forms\Components\TextInput::make('precio_bs')
                            ->numeric(),
                        Forms\Components\TextInput::make('precio_dolar')
                            ->numeric(),
                    ])
                    ->createOptionAction(fn($action) => $action->modalWidth(MaxWidth::Small))
                    ->afterStateUpdated(function (RelationManager $livewire, Get $get, Set $set) {
                        $factura = $livewire->getOwnerRecord();
                        $articulos_id = $get('articulos_id');
                        $articulo = Articulo::find($articulos_id);
                        if ($articulo) {
                            $set('descripcion', $articulo->descripcion);
                            if ($factura->moneda == 'USD') {
                                $set('precio', $articulo->precio_dolar);
                            } else {
                                $set('precio', $articulo->precio_bs);
                            }
                        }
                    }),
                Forms\Components\Hidden::make('descripcion')
                    ->required(),
                Forms\Components\TextInput::make('cantidad')
                    ->numeric()
                    ->required(),
                Forms\Components\Hidden::make('moneda')
                    ->default(fn(RelationManager $livewire): string => $livewire->getOwnerRecord()->moneda)
                    ->required(),
                Forms\Components\TextInput::make('precio')
                    ->numeric()
                    ->required()
                    ->readOnly(fn(Get $get) => !$get('articulos_id')),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descripcion')
            ->columns([
                Tables\Columns\TextColumn::make('descripcion'),
                Tables\Columns\TextColumn::make('precio')
                    ->prefix(fn(Item $item): string => $item->moneda . " ")
                    ->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('cantidad')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Sub Total')
                    ->numeric(decimalPlaces: 2)
                    ->default(function (Item $item) {
                        $precio = $item->precio;
                        $cantidad = $item->cantidad;
                        $total = $precio * $cantidad;
                        return $total;
                    })
                    ->alignEnd(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar item')
                    ->modalHeading('Agregar item')
                    ->modalWidth(MaxWidth::Medium)
                    ->after(function (RelationManager $livewire) {
                        // Runs after the form fields are saved to the database.
                        $this->actualizarTotal($livewire);
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->modalWidth(MaxWidth::Medium)
                        ->after(function (RelationManager $livewire) {
                            // Runs after the form fields are saved to the database.
                            $this->actualizarTotal($livewire);
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->after(function (RelationManager $livewire) {
                            // Runs after the form fields are saved to the database.
                            $this->actualizarTotal($livewire);
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function (RelationManager $livewire) {
                            // Runs after the form fields are saved to the database.
                            $this->actualizarTotal($livewire);
                        }),
                ]),
            ]);
    }

    protected function actualizarTotal(RelationManager $livewire): void
    {
        $id = $livewire->getOwnerRecord()->id;
        $items = Item::where('facturas_id', $id)->get();
        $total = 0;
        foreach ($items as $item) {
            $precio = $item->precio;
            $cantidad = $item->cantidad;
            $total = $total + ($precio * $cantidad);
        }
        $factura = Factura::find($id);
        $factura->total = $total;
        $factura->save();
    }
}
