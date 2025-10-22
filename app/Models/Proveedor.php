<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'empresa',
        'email',
        'telefono',
        'direccion',
        'rfc',
        'activo',
    ];
    

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación con Compras
    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}