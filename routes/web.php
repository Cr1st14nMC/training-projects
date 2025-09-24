<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;

Route::get('/', [ProductoController::class, 'index']);
Route::resource('productos', ProductoController::class);


//Route::get('/saludo', 'App\Http\Controllers\HolaController@index')->name('saludo.index');

