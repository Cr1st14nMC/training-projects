<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\InventarioController;

// Ruta principal que muestra el login o el dashboard
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

// Aquí puedes agregar el resto de tus rutas que usan Blade y Vue
Route::get('/ventas/create', function() {
    return view('ventas.create');
})->name('ventas.create');

Route::get('/ventas', function() {
    return view('ventas.index');
})->name('ventas.index');

// Route::resource('proveedores', ProveedorController::class);
// Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
// Route::get('/compras/create', [CompraController::class, 'create'])->name('compras.create');
// Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');

// Route::resource('compras', CompraController::class);
// ✅ ASEGÚRATE DE QUE ESTAS TRES LÍNEAS ESTÉN PRESENTES ✅
Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
Route::get('/compras/create', [CompraController::class, 'create'])->name('compras.create');
Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');

// Route::get('/inventario', [InventarioController::class, 'indexView'])->name('inventario.index');
// Route::get('/inventario/movimientos', [InventarioController::class, 'movimientosView'])->name('inventario.movimientos');

// routes/web.php
Route::get('/inventario', [InventarioController::class, 'indexView'])->name('inventario.index');
Route::get('/inventario/movimientos', [InventarioController::class, 'movimientosView'])->name('inventario.movimientos');

Route::resource('productos', ProductoController::class);
Route::resource('categories', CategoryController::class);
Route::resource('proveedores', ProveedorController::class);

    // Route::get('/proveedores', [ProveedorController::class, 'index']);
    // Route::post('/proveedores', [ProveedorController::class, 'store']);
    // Route::get('/proveedores/{proveedor}', [ProveedorController::class, 'update']);
    // Route::get('/proveedores/{proveedor}', [ProveedorController::class, 'show']);
    // Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy']);


// GET /proveedores (index)
// GET /proveedores/create (create)
// POST /proveedores (store)
// GET /proveedores/{proveedor} (show)
// GET /proveedores/{proveedor}/edit (edit)
// PUT/PATCH /proveedores/{proveedor} (update)
// DELETE /proveedores/{proveedor} (destroy)

// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::get('/productos', function() {
//         return view('productos.index');
//     })->name('productos.index');

//     Route::get('/ventas', function() {
//         return view('ventas.index');
//     })->name('ventas.index');
// });


// Recursos

// Route::get('/proveedores', function() {
//         return view('proveedores.index');
//     })->name('proveedores.index');

// Route::get('/proveedores/create', function() {
//         return view('proveedores.create');
//     })->name('proveedores.create');



// Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');

// Route::get('/', action: [ProductoController::class, 'index']);
//Route::get('/saludo', 'App\Http\Controllers\HolaController@index')->name('saludo.index');

// --- RUTAS DE RECURSO (CRUDs completos) ---
// Esto crea automáticamente las rutas:
