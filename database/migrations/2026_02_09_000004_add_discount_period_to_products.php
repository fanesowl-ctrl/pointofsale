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
        Schema::table('products', function (Blueprint $table) {
            $table->dateTime('discount_start')->nullable()->comment('Waktu mulai diskon')->after('discount_stock');
            $table->dateTime('discount_end')->nullable()->comment('Waktu berakhir diskon')->after('discount_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['discount_start', 'discount_end']);
        });
    }
};
