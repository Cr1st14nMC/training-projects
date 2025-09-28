<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['nombre','slug'];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'category_product', 'category_id', 'product_id');
    }
}