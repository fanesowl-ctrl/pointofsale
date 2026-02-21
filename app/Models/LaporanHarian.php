<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanHarian extends Model
{
    use HasFactory;

    protected $table = 'laporan_harian';

    protected $fillable = [
        'tanggal',
        'total_transaksi',
        'barang_terjual',
        'total_penjualan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
