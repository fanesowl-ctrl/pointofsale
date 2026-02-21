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
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity')->comment('Sisa stok di batch ini');
            $table->integer('original_quantity')->comment('Jumlah awal stok saat masuk'); // Berguna untuk history/audit
            $table->decimal('cost_price', 15, 2)->default(0)->comment('Harga beli per unit untuk batch ini');
            $table->date('received_at')->comment('Tanggal barang masuk (FIFO key)');
            $table->date('expiry_date')->nullable()->comment('Tanggal kedaluwarsa (Opsional)');
            $table->string('batch_code')->nullable()->comment('Kode produksi/lot');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
