<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;



class CategoryController extends Controller
{
    /**
     * Mostrar listado de categorías.
     */
    public function index(Request $request)
    {
        // puedes paginar si lo deseas -> paginate(10)
        $categories = Category::orderBy('nombre', 'asc')->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $categories
            ], 200);
        }

        return view('category.index', compact('categories'));
    }

    /**
     * Mostrar formulario para crear nueva categoría.
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Almacenar nueva categoría.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:categories,nombre',
            'descripcion' => 'nullable|string|max:500'

        ]);

        $category = Category::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null
        ]);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Categoría creada correctamente.'
            ], 201);
        }

        return redirect()->route('category.index')->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Mostrar una categoría (opcional).
     */
    public function show(Category $category, Request $request)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $category
            ], 200);
        }

        return view('categories.show', compact('category'));
    }

    /**
     * Mostrar formulario para editar.
     */
    public function edit(Category $category)
    {
        return view('category.edit', compact('category'));
    }

    /**
     * Actualizar la categoría.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            // ignore current id en unique
            'nombre' => 'required|string|max:255|unique:categories,nombre,' . $category->id,
            'descripcion' => 'nullable|string|max:500'

        ]);

        $category->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null
        ]);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Categoría actualizada correctamente.'
            ], 200);
        }

        return redirect()->route('category.index')->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Eliminar categoría.
     */
    public function destroy(Request $request, Category $category)
    {
        try {
            // si quieres prevenir borrado cuando haya productos asignados,
            // puedes comprobar $category->productos()->exists() y devolver error.
            $category->delete();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoría eliminada correctamente.'
                ], 200);
            }

            return redirect()->route('category.index')->with('success', 'Categoría eliminada correctamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo eliminar la categoría.'
                ], 500);
            }

            return redirect()->route('category.index')->with('error', 'No se pudo eliminar la categoría.');
        }
    }
}
