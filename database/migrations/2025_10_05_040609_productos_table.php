<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Asegúrate de que existe para evitar errores
                // Si la clave foránea fue declarada de otra forma, ajusta
         if (Schema::hasColumn('productos', 'categoria_id')) {
            
            // 1. Opcional: Verificar la clave foránea
            // A veces el nombre de la clave foránea no es estándar. Es más seguro usar:
            try {
                $table->dropForeign(['categoria_id']); 
            } catch (\Exception $e) {
                // Si la clave no existe, ignorar el error de dropForeign
            }

            // 2. Eliminar la columna
            $table->dropColumn('categoria_id');
        }
        });

    }

    /**
     * Reverse the migrations.
     */
public function down(): void
{
     Schema::table('productos', function (Blueprint $table) {
        // Asegúrate de agregar la columna tal como estaba
        $table->foreignId('categoria_id')->nullable()->constrained('categories');
    });
   
}
};
