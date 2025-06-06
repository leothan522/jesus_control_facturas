<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Factura;
use App\Models\Item;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function factuta(Factura $factura)
    {
        $empresa = Empresa::find($factura->empresas_id);
        $items = Item::where('facturas_id', $factura->id)->get();

        /*return view('export.factura')
            ->with('factura', $factura)
            ->with('empresa', $empresa);*/

        return Pdf::loadView('export.factura', [
            'empresa' => $empresa,
            'factura' => $factura,
            'items' => $items
        ])
            ->stream("Factura_$factura->numero.pdf");
    }
}
