<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fecha_venta',
        'total',
        'observaciones',
    ];

    protected $casts = [
        'fecha_venta' => 'date',
        'total' => 'decimal:2',
    ];

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con DetalleVenta
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }
}