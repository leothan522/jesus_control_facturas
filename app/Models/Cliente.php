<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;
    protected $table = "clientes";
    protected $fillable = [
        'rif',
        'nombre',
        'telefono',
        'email',
        'direccion',
    ];

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class, 'facturas_id', 'id');
    }

}
