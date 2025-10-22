<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    // Vista principal del inventario
    public function index(Request $request)
    {
        $query = Producto::with('categories', 'proveedor');

        // Filtros
        if ($request->has('stock_bajo')) {
            $query->whereRaw('cantidad <= stock_minimo');
        }

        if ($request->has('sin_stock')) {
            $query->where('cantidad', 0);
        }

        if ($request->has('categoria_id')) {
            $query->where('category_id', $request->categoria_id);
        }

        if ($request->has('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        $productos = $query->orderBy('id', 'asc')->get();

        // Estadísticas generales
        $estadisticas = [
            'total_productos' => Producto::count(),
            'stock_bajo' => Producto::whereRaw('cantidad <= stock_minimo')->count(),
            'sin_stock' => Producto::where('cantidad', 0)->count(),
            'valor_inventario' => Producto::sum(DB::raw('cantidad * precio')),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'productos' => $productos,
                'estadisticas' => $estadisticas
            ]);
        }

return response()->json([
            'productos' => $productos, // <-- Ahora $productos incluirá el objeto 'proveedor'
            'estadisticas' => $estadisticas
        ]);    }

    // Historial de movimientos
 public function movimientos(Request $request)
{
    $query = MovimientoInventario::with(['producto', 'user'])
        ->orderBy('created_at', 'desc');

    // Filtros
    if ($request->has('producto_id')) {
        $query->where('producto_id', $request->producto_id);
    }

    if ($request->has('tipo')) {
        $query->where('tipo', $request->tipo);
    }

    if ($request->has('fecha_desde')) {
        $query->whereDate('created_at', '>=', $request->fecha_desde);
    }

    if ($request->has('fecha_hasta')) {
        $query->whereDate('created_at', '<=', $request->fecha_hasta);
    }

    $movimientos = $query->paginate(50);

    // Normalizar: si el movimiento no tiene created_at, usar fecha_compra o fecha_venta si existen
    $movimientos->getCollection()->transform(function($m) {
        // Si created_at vacío y existe fecha_compra o fecha_venta, asignar
        if (empty($m->created_at)) {
            if (!empty($m->fecha_compra)) {
                $m->created_at = $m->fecha_compra;
            } elseif (!empty($m->fecha_venta)) {
                $m->created_at = $m->fecha_venta;
            }
        }
        // Además exponer campos legibles si quieres
        // $m->display_date = $m->created_at ?? $m->fecha_compra ?? $m->fecha_venta ?? null;
        return $m;
    });

    if ($request->wantsJson()) {
        return response()->json($movimientos);
    }

    return view('inventario.movimientos');
}
    // Ajuste manual de inventario
    public function ajustar(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'nuevo_stock' => 'required|integer|min:0',
            'observaciones' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $producto = Producto::findOrFail($validated['producto_id']);
            $stockAnterior = $producto->cantidad ?? 0;
            $diferencia = $validated['nuevo_stock'] - $stockAnterior;

            // Actualizar stock del producto
            $producto->cantidad = $validated['nuevo_stock'];
            $producto->save();

            // Registrar movimiento
            MovimientoInventario::create([
                'producto_id' => $producto->id,
                'tipo' => 'ajuste',
                'cantidad' => abs($diferencia),
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $validated['nuevo_stock'],
                'referencia_tipo' => 'ajuste_manual',
                'user_id' => auth()->id() ?? \App\Models\User::first()->id,
                'observaciones' => $validated['observaciones'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inventario ajustado correctamente',
                'producto' => $producto
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al ajustar inventario: ' . $e->getMessage()
            ], 500);
        }
    }

    // Reporte de productos con stock bajo
    public function stockBajo(Request $request)
    {
        $productos = Producto::with('categories')
            ->whereRaw('cantidad <= stock_minimo')
            ->orderBy('cantidad')
            ->get();

        if ($request->wantsJson()) {
            return response()->json($productos);
        }

        return view('inventario.stock-bajo', compact('productos'));
    }

    // Vista web para inventario
    public function indexView()
    {
        return view('inventario.index');
    }

    public function movimientosView()
    {
        return view('inventario.movimientos');
    }
}