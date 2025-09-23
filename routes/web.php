<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HolaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/saludo', [HolaController::class, 'index'])->name('saludo.index');

//Route::get('/saludo', 'App\Http\Controllers\HolaController@index')->name('saludo.index');

