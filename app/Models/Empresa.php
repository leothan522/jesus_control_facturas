<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use SoftDeletes;
    protected $table = 'empresas';
    protected $fillable = [
        'rif',
        'nombre',
        'telefono',
        'email',
        'direccion',
        'image',
    ];

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class, 'facturas_id', 'id');
    }

}
