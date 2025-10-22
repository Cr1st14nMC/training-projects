<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MovimientoInventario;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $compras = Compra::with(['proveedor', 'productos'])
            ->orderBy('fecha_compra', 'asc')
            ->get();

        if ($request->wantsJson()) {
            return response()->json($compras);
        }

        return view('compras.index');
    }

    public function create()
    {
        return view('compras.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_compra' => 'required|date',
            'notas' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.costo_unitario' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Calcular el total
            $total = 0;
            foreach ($validated['productos'] as $item) {
                $total += $item['cantidad'] * $item['costo_unitario'];
            }

            // 2. Crear la compra
            $compra = Compra::create([
                'proveedor_id' => $validated['proveedor_id'],
                'fecha_compra' => $validated['fecha_compra'],
                'total' => $total,
                'notas' => $validated['notas'] ?? null,
            ]);

            // 3. Registrar los productos en la tabla pivote y actualizar el stock
            foreach ($validated['productos'] as $item) {
                // Adjuntar a la tabla pivote compra_producto
                $compra->productos()->attach($item['producto_id'], [
                    'cantidad' => $item['cantidad'],
                    'costo_unitario' => $item['costo_unitario'],
                ]);

                // Actualizar el stock del producto
                $producto = Producto::find($item['producto_id']);
                if ($producto->stock !== null) {
                    $producto->stock += $item['cantidad'];
                } elseif ($producto->cantidad !== null) {
                    $producto->cantidad += $item['cantidad'];
                }
                $producto->save();

                MovimientoInventario::registrar(
                    $item['producto_id'],
                    'entrada',
                    $item['cantidad'],
                    $compra->id,
                    'compra',
                    "Compra #{$compra->id} - Proveedor: {$compra->proveedor->nombre}"
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compra registrada exitosamente.',
                'compra' => $compra->load('proveedor', 'productos')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la compra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $compra = Compra::with('productos')->findOrFail($id);

            // Restar el stock de los productos
            foreach ($compra->productos as $producto) {
                $cantidad = $producto->pivot->cantidad;

                if ($producto->stock !== null) {
                    $producto->stock -= $cantidad;
                } elseif ($producto->cantidad !== null) {
                    $producto->cantidad -= $cantidad;
                }
                $producto->save();
            }

            $compra->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compra eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la compra: ' . $e->getMessage()
            ], 500);
        }
    }
}