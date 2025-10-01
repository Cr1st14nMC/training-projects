<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Category;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'categories_id', // <-- Agregar esto
        'precio',
    ];
     public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_product', 'product_id', 'categories_id');
    }
    
}
    