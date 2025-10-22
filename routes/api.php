<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\InventarioController;

// Rutas públicas (sin autenticación)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/logout', [AuthController::class, 'logout']);

// Productos
Route::get('/productos', [ProductoController::class, 'index']);
Route::post('/productos', [ProductoController::class, 'store']);
Route::put('/productos/{producto}', [ProductoController::class, 'update']);
Route::delete('/productos/{producto}', [ProductoController::class, 'destroy']);

// Categorías
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{categorie}', [CategoryController::class, 'update']);
Route::delete('/categories/{categorie}', [CategoryController::class, 'destroy']);

// Ventas
// Route::resource(name: 'ventas', VentaController::class);
Route::get('/ventas', [VentaController::class, 'index']);
Route::post('/ventas', [VentaController::class, 'store']);
Route::get('/ventas/{id}', [VentaController::class, 'show']);
Route::delete('/ventas/{id}', [VentaController::class, 'destroy']);

// Inventario
Route::get('/inventario', [InventarioController::class, 'index']);
Route::get('/inventario/movimientos', [InventarioController::class, 'movimientos']);
// Route::post('/inventario/ajustar', [InventarioController::class, 'ajustar']);
// Route::get('/inventario/stock-bajo', [InventarioController::class, 'stockBajo']);

Route::apiResource('proveedores', ProveedorController::class);
Route::apiResource('compras', CompraController::class);

// Route::apiResource('inventario', InventarioController::class);

// Route::get('/compras', [VentaController::class, 'index']);
// Route::post('/compras', [VentaController::class, 'store']);
// Route::get('/compras/{id}', [VentaController::class, 'show']);
// Route::delete('/compras/{id}', [VentaController::class, 'destroy']);

// Route::get('/proveedores', [ProveedorController::class, 'index']);
// Route::post('/proveedores', [ProveedorController::class, 'store']);
// Route::get('/proveedores/{proveedor}', [ProveedorController::class, 'update']);
// Route::get('/proveedores/{proveedor}', [ProveedorController::class, 'show']);
// Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy']);
