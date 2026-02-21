<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Script ini akan mengupdate semua produk existing yang belum memiliki
     * nilai final_price, dengan mengisi final_price = selling_price
     */
    public function up(): void
    {
        // Update produk yang belum memiliki final_price
        DB::table('products')
            ->whereNull('final_price')
            ->orWhere('final_price', 0)
            ->update([
                'final_price' => DB::raw('selling_price'),
                'discount_percentage' => 0,
                'discount_amount' => 0,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu rollback karena ini hanya update data
    }
};
