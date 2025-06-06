<?php

namespace App\Filament\Resources\FacturaResource\Widgets;

use App\Models\Factura;
use App\Models\Item;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;

class FacturaWidget extends BaseWidget
{
    public ?Model $record = null;
    protected function getStats(): array
    {
        $total = $this->record->total;
        $moneda = $this->record->moneda;
        return [
            Stat::make('Total Factura', $moneda.' '.formatoMillares($total)),
        ];
    }

    #[On('updatePage')]
    public function updatePage(): void
    {
        $facturas_id = $this->record->id;
        $moneda = $this->record->moneda;
        $items = Item::where('facturas_id', $facturas_id)->get();
        $total = 0;
        foreach ($items as $item) {
            $item->moneda = $moneda;
            $item->save();
            $precio = $item->precio;
            $cantidad = $item->cantidad;
            $total = $total + ($precio * $cantidad);
        }
        $factura = Factura::find($facturas_id);
        $factura->total = $total;
        $factura->save();
        $this->dispatch('updateTable');
    }
}
