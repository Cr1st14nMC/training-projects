<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\MovimientoInventario;

class VentaController extends Controller
{
    // Listar todas las ventas
    public function index(Request $request)
    {
        $ventas = Venta::with(['detalles.producto', 'user'])
            ->orderBy('id', 'asc')
            ->get();

        if ($request->wantsJson()) {
            return response()->json($ventas);
        }

        return view('ventas.index');
    }

    // Crear una nueva venta
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_venta' => 'required|date',
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Calcular el total y validar stock
            $total = 0;
            $detallesData = [];

            foreach ($validated['productos'] as $item) {
                $producto = Producto::findOrFail($item['producto_id']);

                // Validar que haya suficiente stock
                $stockDisponible = $producto->stock ?? $producto->cantidad ?? 0;
                if ($item['cantidad'] > $stockDisponible) {
                    throw new \Exception("No hay suficiente stock para el producto: {$producto->nombre}. Disponible: {$stockDisponible}");
                }

                $subtotal = $producto->precio * $item['cantidad'];
                $total += $subtotal;

                $detallesData[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotal,
                ];
            }

            // Crear la venta
            $userId = Auth::id() ?? \App\Models\User::first()->id;

            $venta = Venta::create([
                'user_id' => $userId,
                'fecha_venta' => $validated['fecha_venta'],
                'total' => $total,
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            // Crear los detalles y actualizar stock
            foreach ($detallesData as $detalle) {
                $venta->detalles()->create($detalle);

                // Reducir el stock del producto
                $producto = Producto::find($detalle['producto_id']);
                if ($producto->stock !== null) {
                    $producto->stock -= $detalle['cantidad'];
                } elseif ($producto->cantidad !== null) {
                    $producto->cantidad -= $detalle['cantidad'];
                }
                $producto->save();
                MovimientoInventario::registrar(
                    $detalle['producto_id'],
                    'salida',
                    $detalle['cantidad'],
                    $venta->id,
                    'venta',
                    "Venta #{$venta->id}"
                );
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
            DB::beginTransaction();

            $venta = Venta::with('detalles')->findOrFail($id);

            // Devolver el stock de los productos
            foreach ($venta->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                if ($producto->stock !== null) {
                    $producto->stock += $detalle->cantidad;
                } elseif ($producto->cantidad !== null) {
                    $producto->cantidad += $detalle->cantidad;
                }
                $producto->save();
            }

            $venta->delete();

            DB::commit();

            return response()->json([
                'message' => 'Venta eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar la venta',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}