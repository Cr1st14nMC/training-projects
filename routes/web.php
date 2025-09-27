<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;

Route::get('/', function () {
    $productos = \App\Models\Producto::all();
    return view('productos.index', compact('productos'));

});

Route::resource('productos', ProductoController::class);


// Route::get('/', function () {
//     $productos = \App\Models\Producto::all();
//     return view('productos.index', compact('productos'));

// });





// Route::get('/', action: [ProductoController::class, 'index']);
//Route::get('/saludo', 'App\Http\Controllers\HolaController@index')->name('saludo.index');

