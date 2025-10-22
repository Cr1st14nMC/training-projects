<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::query();
        

        if ($q = $request->query('q')) {
            // Búsqueda actualizada
            $query->where('nombre', 'like', "%{$q}%")
                ->orWhere('empresa', 'like', "%{$q}%")
                ->orWhere('rfc', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%");
        }

        $proveedores = $query->orderBy('id')->get();
        

        if ($request->wantsJson()) {
            return response()->json(['data' => $proveedores]);
        }

        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');

        //      public function create(Request $request)
        // {
        //     $categories = Category::orderBy('nombre')->get();
        //     return view('productos.create', compact('categories'));
        //     // Si quieres que la vista create sea cargada por Vue desde /productos/create
        // }
    }

    public function store(Request $request)
    {
        // Validación actualizada
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'empresa' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:proveedores,email',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'rfc' => 'nullable|string|max:13',
            'activo' => 'required|boolean',
        ]);

        $proveedore = Proveedor::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $proveedore], 201);
        }

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado.');
    }

    public function edit(Proveedor $proveedore) // Cambiado a $proveedor
    {
        return view('proveedores.edit', ['proveedor' => $proveedore]);
    }

    public function update(Request $request, Proveedor $proveedore)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'empresa' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:proveedores,email,' . $proveedore->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'rfc' => 'nullable|string|max:13',
            'activo' => 'required|boolean',
        ]);

        $proveedore->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $proveedore]);
        }

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado.');
    }
    public function show(Proveedor $proveedore) // Cambiado a $proveedor
    {
        return response()->json(['data' => $proveedore]);
    }
    public function destroy(Request $request, Proveedor $proveedore) // Cambiado a $proveedor
    {
        $proveedore->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Proveedor eliminado.']);
        }

        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado.');
    }
}