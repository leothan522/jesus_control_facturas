<?php

namespace App\Filament\Resources\FacturaResource\Pages;

use App\Filament\Resources\FacturaResource;
use App\Models\Empresa;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFactura extends CreateRecord
{
    protected static string $resource = FacturaResource::class;
    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $empresas_id = $data['empresas_id'];
        $empresa= Empresa::find($empresas_id);
        if ($empresa){
            $numero = $empresa->correlativo_factura ?? 1;
            $empresa->correlativo_factura = $numero + 1;
            $empresa->save();
        }
       return $data;
    }
}
