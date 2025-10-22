<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            // Verifica si las columnas ya existen antes de agregarlas
            if (!Schema::hasColumn('detalle_ventas', 'cantidad')) {
                $table->integer('cantidad')->default(1)->after('producto_id');
            }
            if (!Schema::hasColumn('detalle_ventas', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->after('precio_unitario');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropColumn(['cantidad', 'subtotal']);
        });
    }
};