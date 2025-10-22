<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Category;
use App\Models\Proveedor;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'cantidad',
        'proveedor_id', // <-- 2. Añade la nueva columna aquí
    ];
     public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_product', 'product_id', 'categories_id');
    }

    public function proveedor()
     {
         // Asume que tu modelo se llama 'Proveedor'
         return $this->belongsTo(Proveedor::class, 'proveedor_id');
     }
    
}
    