<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->enum('tipo', ['entrada', 'salida', 'ajuste']); // entrada=compra, salida=venta, ajuste=corrección manual
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->string('referencia_tipo')->nullable(); // 'compra', 'venta', 'ajuste_manual'
            $table->unsignedBigInteger('referencia_id')->nullable(); // ID de la compra o venta
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index(['producto_id', 'created_at']);
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};