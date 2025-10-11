<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;

// Ruta principal - Login (Público)
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::get('/productos', function() {
//         return view('productos.index');
//     })->name('productos.index');

//     Route::get('/ventas', function() {
//         return view('ventas.index');
//     })->name('ventas.index');
// });

 Route::get('/ventas', function() {
        return view('ventas.index');
    })->name('ventas.index');

Route::get('/ventas/create', function() {
        return view('ventas.create');
    })->name('ventas.create');
// Recursos

Route::resource('productos', ProductoController::class);
Route::resource('categories', CategoryController::class);


// Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');

// Route::get('/', action: [ProductoController::class, 'index']);
//Route::get('/saludo', 'App\Http\Controllers\HolaController@index')->name('saludo.index');

