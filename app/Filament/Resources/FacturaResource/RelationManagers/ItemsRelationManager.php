<?php

namespace App\Filament\Resources\FacturaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
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
                    ->createOptionForm([
                        Forms\Components\TextInput::make('descripcion')
                            ->unique()
                            ->maxLength(255)
                            ->required(),
                        Forms\Components\TextInput::make('precio_bs')
                            ->numeric(),
                        Forms\Components\TextInput::make('precio_dolar')
                            ->numeric(),
                    ]),
                Forms\Components\TextInput::make('descripcion')
                    ->required(),
                Forms\Components\TextInput::make('cantidad')
                    ->integer()
                    ->required(),
                Forms\Components\TextInput::make('moneda')
                    ->required(),
                Forms\Components\TextInput::make('precio')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descripcion')
            ->columns([
                Tables\Columns\TextColumn::make('descripcion'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
