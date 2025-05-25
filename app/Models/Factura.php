<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    use SoftDeletes;
    protected $table = 'facturas';
    protected $fillable = [
        'numero',
        'fecha',
        'moneda',
        'es_credito',
        'dias_credito',
        'empresas_id',
        'empresa_rif',
        'empresa_nombre',
        'empresa_email',
        'empresa_direccion',
        'empresa_image',
        'clientes_id',
        'cliente_rif',
        'cliente_nombre',
        'cliente_telefono',
        'cliente_email',
        'cliente_direccion',
        'sub_total',
        'total',
        'estatus',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'facturas_id', 'id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'clientes_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'facturas_id', 'id');
    }

}
