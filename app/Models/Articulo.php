<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Articulo extends Model
{
    use SoftDeletes;
    protected $table = 'articulos';
    protected $fillable = [
        'descripcion',
        'precio_bs',
        'precio_dolar',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'articulos_id', 'id');
    }

}
