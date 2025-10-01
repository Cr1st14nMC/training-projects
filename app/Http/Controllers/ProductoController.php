<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Category;    

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $q = $request->query('q');
    $query = Producto::query();

    if ($q) {
        $query->where(function ($sub) use ($q) {
            $sub->where('id', 'like', "%{$q}%")
                ->orWhere('nombre', 'like', "%{$q}%");
        });
    }

    $productos = $query->orderBy('id', 'asc')->get();

    if ($request->wantsJson() || $request->is('api/*')) {
        return response()->json([
            'success' => true,
            'data' => $productos
        ], 200);
    }

    return view('productos.index', compact('productos'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $categories = Category::orderBy('nombre')->get();
        return view('productos.create', compact('categories'));
        // Si quieres que la vista create sea cargada por Vue desde /productos/create
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categories_id' => 'nullable|exists:categories,id',
            'precio' => 'required|numeric|min:0',
        ]);

        $producto = Producto::create($validated);

        if ($request->has('categories')) {
            $producto->categories()->sync($request->input('categories'));
        }

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
    public function edit(Producto $producto)
    {
        // Cargar todas las categorías
        $categories = Category::orderBy('nombre', 'asc')->get();
        
        return view('productos.edit', compact('producto', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'categoria_id' => 'required|exists:categories,id',
            'precio' => 'required|numeric|min:0'
        ]);

        $producto->update($validated);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $producto,
                'message' => 'Producto actualizado correctamente.'
            ], 200);
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
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
