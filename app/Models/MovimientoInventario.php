<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'producto_id',
        'tipo',
        'cantidad',
        'stock_anterior',
        'stock_nuevo',
        'referencia_tipo',
        'referencia_id',
        'user_id',
        'observaciones',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'stock_anterior' => 'integer',
        'stock_nuevo' => 'integer',
    ];

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    // Relación con Usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Método para registrar un movimiento automáticamente
    public static function registrar($productoId, $tipo, $cantidad, $referenciaId = null, $referenciaTipo = null, $observaciones = null)
    {
        $producto = Producto::findOrFail($productoId);
        $stockAnterior = $producto->stock ?? $producto->cantidad ?? 0;
        
        // Calcular nuevo stock
        if ($tipo === 'entrada') {
            $stockNuevo = $stockAnterior + $cantidad;
        } elseif ($tipo === 'salida') {
            $stockNuevo = $stockAnterior - $cantidad;
        } else { // ajuste
            $stockNuevo = $cantidad; // En ajustes, la cantidad es el nuevo stock total
        }

        return self::create([
            'producto_id' => $productoId,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $stockNuevo,
            'referencia_id' => $referenciaId,
            'referencia_tipo' => $referenciaTipo,
            'user_id' => auth()->id() ?? \App\Models\User::first()->id,
            'observaciones' => $observaciones,
        ]);
    }
}