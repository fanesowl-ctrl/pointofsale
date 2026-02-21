<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder ini akan menambahkan beberapa contoh produk dengan diskon
     * untuk testing fitur diskon
     */
    public function run(): void
    {
        $products = [
            [
                'product_code' => 'DISC001',
                'name' => 'Produk Diskon 10%',
                'cost_price' => 50000,
                'selling_price' => 100000,
                'discount_percentage' => 10,
                'discount_amount' => 10000,
                'final_price' => 90000,
                'stock' => 50,
            ],
            [
                'product_code' => 'DISC002',
                'name' => 'Produk Diskon 25%',
                'cost_price' => 75000,
                'selling_price' => 150000,
                'discount_percentage' => 25,
                'discount_amount' => 37500,
                'final_price' => 112500,
                'stock' => 30,
            ],
            [
                'product_code' => 'DISC003',
                'name' => 'Produk Diskon 50% (Flash Sale)',
                'cost_price' => 100000,
                'selling_price' => 200000,
                'discount_percentage' => 50,
                'discount_amount' => 100000,
                'final_price' => 100000,
                'stock' => 10,
            ],
            [
                'product_code' => 'NODISC001',
                'name' => 'Produk Tanpa Diskon',
                'cost_price' => 60000,
                'selling_price' => 120000,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'final_price' => 120000,
                'stock' => 100,
            ],
        ];

        foreach ($products as $product) {
            // Cek apakah product_code sudah ada
            $exists = DB::table('products')
                ->where('product_code', $product['product_code'])
                ->exists();

            if (!$exists) {
                DB::table('products')->insert(array_merge($product, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]));
            }
        }

        $this->command->info('✅ Berhasil menambahkan contoh produk dengan diskon!');
    }
}
