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
        // Usa Schema::table para MODIFICAR la tabla existente
        Schema::table('productos', function (Blueprint $table) {
            // Agrega SOLAMENTE la nueva columna
            $table->integer('cantidad')->default(0)->after('precio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Define la acción contraria: eliminar la columna si se revierte la migración
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('cantidad');
        });
    }
};