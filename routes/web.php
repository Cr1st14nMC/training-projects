<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoryController;


Route::get('/', function () {
    $productos = \App\Models\Producto::all();
    return view('productos.index', compact('productos'));

});

// Route::get('/', function () {
//     $productos = \App\Models\Producto::all();
//     return view('home', compact('productos'));
// });


Route::resource('productos', ProductoController::class);
Route::resource('categories', CategoryController::class);


// Route::get('/', function () {
//     $productos = \App\Models\Producto::all();
//     return view('productos.index', compact('productos'));

// });





// Route::get('/', action: [ProductoController::class, 'index']);
//Route::get('/saludo', 'App\Http\Controllers\HolaController@index')->name('saludo.index');

