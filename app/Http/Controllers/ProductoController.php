<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $productos = Producto::orderBy('id','asc')->get();

        // Si la petición espera JSON (API / AJAX), devolver JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $productos
            ], 200);
        }

        // Si es petición normal, renderizar Blade
        return view('productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Si quieres que la vista create sea cargada por Vue desde /productos/create
        return view('productos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
        ]);

        $producto = Producto::create($validated);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $producto,
                'message' => 'Producto creado correctamente.'
            ], 201);
        }

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $producto = Producto::findOrFail($id);
        return view('productos.edit', compact('producto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
        ]);

        $producto->update($validated);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $producto,
                'message' => 'Producto actualizado'
            ], 200);
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Producto $producto)
    {
        try {
            $producto->delete();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto eliminado correctamente.'
                ], 200);
            }

            return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo eliminar el producto.'
                ], 500);
            }

            return redirect()->route('productos.index')->with('error', 'No se pudo eliminar el producto.');
        }
    }
}
