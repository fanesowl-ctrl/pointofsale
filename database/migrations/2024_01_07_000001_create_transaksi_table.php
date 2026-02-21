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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique(); // ID Transaksi
            $table->date('tanggal'); // Tanggal
            $table->decimal('sub_total', 15, 2); // Sub Total
            $table->string('kasir_name')->nullable(); // Helper to store who made it
            $table->timestamps();
        });

        Schema::create('transaksi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products'); // Link to product
            $table->integer('quantity'); // Total stok yang dibeli
            $table->decimal('price', 15, 2); // Harga barang saat transaksi
            $table->decimal('total', 15, 2); // Total harga (qty * price)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_details');
        Schema::dropIfExists('transaksi');
    }
};
