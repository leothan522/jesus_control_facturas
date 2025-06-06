<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacturaResource\Pages;
use App\Filament\Resources\FacturaResource\RelationManagers;
use App\Filament\Resources\FacturaResource\Widgets\FacturaWidget;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Factura;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class FacturaResource extends Resource
{
    protected static ?string $model = Factura::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-minus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make()
                    ->schema([
                        Forms\Components\TextInput::make('numero')
                            ->default(function () {
                                $empresa = Empresa::find(1);
                                if ($empresa) {
                                    $numero = $empresa->correlativo_factura ?? 1;
                                    return $empresa->formato_factura . "" . cerosIzquierda($numero, 4);
                                }
                                return null;
                            })
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('fecha')
                            ->required(),
                        Forms\Components\Select::make('moneda')
                            ->options([
                                'Bs.' => 'Bs.',
                                'USD' => 'USD'
                            ])
                            ->required()
                    ])
                    ->columns(3),
                Forms\Components\Fieldset::make()
                    ->schema([
                        Forms\Components\Select::make('clientes_id')
                            ->label('Cliente')
                            ->relationship('cliente', 'nombre')
                            ->searchable(['rif', 'nombre'])
                            ->preload()
                            ->required()
                            ->live()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('rif')
                                    ->unique()
                                    ->maxLength(255)
                                    ->required(),
                                Forms\Components\TextInput::make('nombre')
                                    ->maxLength(255)
                                    ->required(),
                                Forms\Components\TextInput::make('telefono')
                                    ->tel()
                                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
                                Forms\Components\TextInput::make('email')
                                    ->email(),
                                Forms\Components\Textarea::make('direccion'),
                            ])
                            ->createOptionModalHeading('Nuevo Cliente')
                            ->createOptionAction(fn($action) => $action->modalWidth(MaxWidth::Small))
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $cliente = Cliente::find($get('clientes_id'));
                                if ($cliente) {
                                    $set('cliente_rif', $cliente->rif);
                                    $set('cliente_nombre', $cliente->nombre);
                                    $set('cliente_telefono', $cliente->telefono);
                                    $set('cliente_email', $cliente->email);
                                    $set('cliente_direccion', $cliente->direccion);
                                } else {
                                    $set('cliente_rif', '');
                                    $set('cliente_nombre', '');
                                    $set('cliente_telefono', '');
                                    $set('cliente_email', '');
                                    $set('cliente_direccion', '');
                                }
                            })
                            ->columnSpan(2),
                        Forms\Components\Toggle::make('es_credito')
                            ->inline(false)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $isCredito = $get('es_credito');
                                if (!$isCredito) {
                                    $set('dias_credito', '');
                                }
                            }),
                        Forms\Components\TextInput::make('dias_credito')
                            ->numeric()
                            ->requiredIf('es_credito', true)
                            ->readOnly(fn(Get $get) => !$get('es_credito')),
                    ])
                    ->columns(4),
                Forms\Components\Hidden::make('empresas_id')
                    ->default(1),
                Forms\Components\Hidden::make('empresa_rif')
                    ->default(function () {
                        $empresa = Empresa::find(1);
                        if ($empresa) {
                            return $empresa->rif;
                        }
                        return null;
                    }),
                Forms\Components\Hidden::make('empresa_nombre')
                    ->default(function () {
                        $empresa = Empresa::find(1);
                        if ($empresa) {
                            return $empresa->nombre;
                        }
                        return null;
                    }),
                Forms\Components\Hidden::make('empresa_telefono')
                    ->default(function () {
                        $empresa = Empresa::find(1);
                        if ($empresa) {
                            return $empresa->telefono;
                        }
                        return null;
                    }),
                Forms\Components\Hidden::make('empresa_email')
                    ->default(function () {
                        $empresa = Empresa::find(1);
                        if ($empresa) {
                            return $empresa->email;
                        }
                        return null;
                    }),
                Forms\Components\Hidden::make('empresa_direccion')
                    ->default(function () {
                        $empresa = Empresa::find(1);
                        if ($empresa) {
                            return $empresa->direccion;
                        }
                        return null;
                    }),
                Forms\Components\Hidden::make('empresa_image')
                    ->default(function () {
                        $empresa = Empresa::find(1);
                        if ($empresa) {
                            return $empresa->image;
                        }
                        return null;
                    }),
                Forms\Components\Hidden::make('cliente_rif'),
                Forms\Components\Hidden::make('cliente_nombre'),
                Forms\Components\Hidden::make('cliente_telefono'),
                Forms\Components\Hidden::make('cliente_email'),
                Forms\Components\Hidden::make('cliente_direccion'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cliente_rif')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente_nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->prefix(fn($record): string => $record->moneda . " ")
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd(),
                Tables\Columns\IconColumn::make('estatus')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estatus')
                    ->options([
                        '0' => 'Pago Pendiente',
                        '1' => 'Factura Cancelada',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('estatus')
                        ->label('Cambiar Estatus')
                        ->icon('heroicon-o-clock')
                        ->action(function (Factura $record): void {
                            $id = $record->getKey();
                            $factura = Factura::find($id);
                            if ($factura) {
                                if ($factura->estatus) {
                                    $factura->estatus = 0;
                                } else {
                                    $factura->estatus = 1;
                                }
                                $factura->save();
                            }
                        }),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->before(fn($record) => $record->update(['numero' => '*' . $record->numero])),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->numero = '*' . $record->numero;
                                $record->save();
                            }
                        }),
                ]),
                ExportBulkAction::make()->exports([
                    ExcelExport::make()->withColumns([
                        Column::make('numero'),
                        Column::make('fecha')
                            ->formatStateUsing(fn ($record) => $record->fecha = Carbon::parse($record->fecha)->format('d-m-Y'))
                            ->format(NumberFormat::FORMAT_DATE_DDMMYYYY),
                        Column::make('cliente_rif'),
                        Column::make('cliente_nombre'),
                        Column::make('moneda'),
                        Column::make('total')->format(NumberFormat::FORMAT_NUMBER_00),
                        Column::make('estatus')
                        ->formatStateUsing(fn($record) => $record->estatus ? 'Factura Cancelada' : 'Pago Pendiente'),
                    ])
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFacturas::route('/'),
            'create' => Pages\CreateFactura::route('/create'),
            'edit' => Pages\EditFactura::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            FacturaWidget::class,
        ];
    }
}
