<?php
// app/Models/Compra.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id',
        'fecha_compra',
        'total',
        'notas',
    ];

    protected $casts = [
        'fecha_compra' => 'datetime',
    ];

    /**
     * Relación: Una compra pertenece a un proveedor.
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Relación: Una compra tiene muchos productos.
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'compra_producto')
                    ->withPivot('cantidad', 'costo_unitario') // Trae estos campos adicionales de la tabla pivote
                    ->withTimestamps();
    }
}
