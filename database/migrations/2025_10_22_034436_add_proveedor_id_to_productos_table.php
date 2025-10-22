<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_proveedor_id_to_productos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Añade la columna de clave foránea
            // Asume que tu tabla de proveedores se llama 'proveedores'
            $table->foreignId('proveedor_id')
                  ->nullable()                  // Permite que un producto no tenga proveedor
                  ->after('cantidad')           // (Opcional) La pone después de la columna cantidad
                  ->constrained('proveedores')  // Enlaza con la tabla 'proveedores'
                  ->onDelete('set null');        // Si se borra el proveedor, pone null en el producto
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Esto revierte los cambios si haces 'migrate:rollback'
            $table->dropForeign(['proveedor_id']);
            $table->dropColumn('proveedor_id');
        });
    }
};