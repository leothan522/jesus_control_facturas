<?php

namespace App\Filament\Resources\FacturaResource\Pages;

use App\Filament\Resources\FacturaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFactura extends EditRecord
{
    protected static string $resource = FacturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            FacturaResource\Widgets\FacturaWidget::class,
        ];
    }

    protected function beforeSave(): void
    {
        // Runs before the form fields are saved to the database.
        $this->dispatch('updatePage');
    }
}
