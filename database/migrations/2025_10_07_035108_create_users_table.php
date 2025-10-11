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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID único para cada usuario
            $table->string('name'); // Nombre del usuario
            $table->string('email')->unique(); // Email, debe ser único
            $table->timestamp('email_verified_at')->nullable(); // Para verificación de email (opcional)
            $table->string('password'); // Contraseña encriptada
            $table->rememberToken(); // Para la función "Recordarme"
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};