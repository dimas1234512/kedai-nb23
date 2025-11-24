<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Optimasi Tabel Orders (Paling sering diakses Dashboard)
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');     // Mempercepat hitungan "Pesanan Baru" / "Lunas"
            $table->index('created_at'); // Mempercepat grafik/filter tanggal
            $table->index('payment_method'); // Mempercepat filter Cash/QRIS
        });

        // Optimasi Tabel Produk
        Schema::table('products', function (Blueprint $table) {
            $table->index('is_available'); // Mempercepat filter menu di HP pelanggan
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at', 'payment_method']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_available']);
        });
    }
};