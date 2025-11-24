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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Menghubungkan produk ke kategori (Makanan/Minuman)
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            
            $table->string('name'); // Nama menu: "Ricebowl Chicken"
            $table->string('slug')->unique(); 
            $table->string('image')->nullable(); // Foto menu (opsional)
            $table->text('description')->nullable(); // Penjelasan menu (opsional)
            
            // Harga pakai integer biar gampang dihitung (misal: 12000)
            $table->integer('price'); 
            
            // Ini fitur "Stok Kosong" yang kamu minta (Default: true/tersedia)
            $table->boolean('is_available')->default(true); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
