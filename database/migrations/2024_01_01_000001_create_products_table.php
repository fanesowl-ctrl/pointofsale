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
            $table->string('product_code')->unique(); // Kode Barang
            $table->string('name'); // Nama Barang
            $table->decimal('cost_price', 15, 2); // Harga Awal (Beli/Modal)
            $table->decimal('selling_price', 15, 2); // Harga Jual
            $table->integer('stock')->default(0); // Stok Barang
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
