<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $table = 'facturas_items';
    protected $fillable = [
        'facturas_id',
        'articulos_id',
        'descripcion',
        'cantidad',
        'moneda',
        'precio',
    ];

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class, 'facturas_id', 'id');
    }

    public function articulo(): BelongsTo
    {
        return $this->belongsTo(Articulo::class, 'articulos_id', 'id');
    }

}


