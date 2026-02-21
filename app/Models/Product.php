<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products'; // Eksplisit nama tabel

    protected $fillable = [
        'product_code',
        'name',
        'category',
        'cost_price',
        'selling_price',
        'discount_percentage',
        'discount_amount',
        'final_price',
        'discount_stock',
        'discount_start',
        'discount_end',
        'stock',
        'image',
    ];

    protected $casts = [
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
    ];

    public function stockBatches()
    {
        return $this->hasMany(StockBatch::class);
    }

    /**
     * Kurangi stok dengan metode FIFO (First In First Out)
     */
    public function reduceStockFifo($quantity)
    {
        $remaining = $quantity;
        
        // Ambil batch stok yang tersedia, urutkan dari yang terlama
        $batches = $this->stockBatches()
            ->where('quantity', '>', 0)
            ->orderBy('received_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;
            
            $take = min($batch->quantity, $remaining);
            $batch->decrement('quantity', $take);
            $remaining -= $take;
        }
        
        // Update stok total (agregat)
        $this->decrement('stock', $quantity);
    }
}
