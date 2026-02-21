<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'nomor_transaksi',
        'tanggal',
        'sub_total',
        'payment_method',
        'kasir_name',
        'member_id',
        'discount_amount',
        'grand_total',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }
}
