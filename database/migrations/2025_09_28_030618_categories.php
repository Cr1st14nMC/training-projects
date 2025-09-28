<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('nombre')->unique();
        $table->string('slug')->nullable();
        $table->timestamps();
    });

    Schema::create('category_product', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained('productos')->onDelete('cascade'); // si tu tabla es productos
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
