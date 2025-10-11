<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    // Listar todas las ventas
    public function index()
    {
        $ventas = Venta::with(['detalles.producto', 'user'])
            ->orderBy('fecha_venta', 'desc')
            ->get();

        return response()->json($ventas);
    }

    // Crear una nueva venta
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_venta' => 'required|date',
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
        ]);

        try {
            DB::beginTransaction();

            // Calcular el total sumando los precios de cada producto
            $total = 0;
            $detallesData = [];

            foreach ($validated['productos'] as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                $total += $producto->precio;

                $detallesData[] = [
                    'producto_id' => $producto->id,
                    'precio_unitario' => $producto->precio,
                ];
            }

            // Crear la venta
            // TEMPORAL: Usa el primer usuario de la base de datos
            // TODO: Implementar autenticación real
            $userId = Auth::id() ?? \App\Models\User::first()->id;
            
            $venta = Venta::create([
                'user_id' => $userId,
                'fecha_venta' => $validated['fecha_venta'],
                'total' => $total,
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            // Crear los detalles
            foreach ($detallesData as $detalle) {
                $venta->detalles()->create($detalle);
            }

            DB::commit();

            return response()->json([
                'message' => 'Venta registrada exitosamente',
                'venta' => $venta->load('detalles.producto')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar la venta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Mostrar una venta específica
    public function show($id)
    {
        $venta = Venta::with(['detalles.producto', 'user'])->findOrFail($id);
        return response()->json($venta);
    }

    // Eliminar una venta
    public function destroy($id)
    {
        try {
            $venta = Venta::findOrFail($id);
            $venta->delete();

            return response()->json([
                'message' => 'Venta eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la venta',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}