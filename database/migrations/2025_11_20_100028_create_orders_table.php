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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name'); // Nama Pemesan
            $table->string('table_number');  // Nomor Meja
            $table->string('payment_method'); // cash / qris
            $table->string('status')->default('pending'); // pending, paid, done
            $table->string('payment_proof')->nullable(); // Bukti transfer (bisa kosong kalau cash)
            $table->integer('total_amount'); // Total Harga
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
