<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'original_quantity',
        'cost_price',
        'received_at',
        'expiry_date',
        'batch_code',
    ];

    protected $casts = [
        'received_at' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Relasi ke Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
