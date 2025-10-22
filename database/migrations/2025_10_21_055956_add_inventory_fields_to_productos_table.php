<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
        // Cantidad mínima para alertas de stock bajo
        $table->integer('stock_minimo')->default(0)->after('cantidad');
        // Costo promedio ponderado del producto
        $table->decimal('costo_promedio', 10, 2)->default(0)->after('precio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            //
        });
    }
};
