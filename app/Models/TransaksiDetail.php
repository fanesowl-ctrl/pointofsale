<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    use HasFactory;

    protected $table = 'transaksi_details';

    protected $fillable = [
        'transaksi_id',
        'product_id',
        'quantity',
        'price',
        'total',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}
