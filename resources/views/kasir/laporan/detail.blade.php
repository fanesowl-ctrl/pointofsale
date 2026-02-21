@extends('layouts.kasir')

@section('title', 'Detail Laporan')

@section('content')
<div style="margin-bottom: 24px;">
    <a href="{{ route('kasir.laporan.index') }}" style="color: #6b7280; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
        <i class="ri-arrow-left-line"></i> Kembali ke Laporan
    </a>
</div>

<div class="card" style="margin-bottom: 24px;">
    <h2 style="font-size: 1.5rem; margin-bottom: 20px;">Detail Laporan Harian</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <div>
            <div style="color: #6b7280; font-size: 0.9rem; margin-bottom: 4px;">Tanggal</div>
            <div style="font-size: 1.25rem; font-weight: 600;">{{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('d F Y') }}</div>
        </div>
        <div>
            <div style="color: #6b7280; font-size: 0.9rem; margin-bottom: 4px;">Total Transaksi</div>
            <div style="font-size: 1.25rem; font-weight: 600;">{{ $laporan->total_transaksi }} transaksi</div>
        </div>
        <div>
            <div style="color: #6b7280; font-size: 0.9rem; margin-bottom: 4px;">Barang Terjual</div>
            <div style="font-size: 1.25rem; font-weight: 600;">{{ number_format($laporan->barang_terjual, 0, ',', '.') }} item</div>
        </div>
        <div>
            <div style="color: #6b7280; font-size: 0.9rem; margin-bottom: 4px;">Total Penjualan</div>
            <div style="font-size: 1.25rem; font-weight: 600; color: #10b981;">Rp {{ number_format($laporan->total_penjualan, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 1.25rem;">Daftar Transaksi</h3>
        <a href="{{ route('kasir.laporan.pdf', $laporan->tanggal) }}" style="background: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="ri-file-pdf-line"></i> Export PDF
        </a>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f9fafb; text-align: left;">
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">No</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">ID Transaksi</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Waktu</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Kasir</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $key => $trx)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px;">{{ $key + 1 }}</td>
                <td style="padding: 12px; font-family: monospace;">{{ $trx->nomor_transaksi }}</td>
                <td style="padding: 12px;">{{ \Carbon\Carbon::parse($trx->created_at)->format('H:i') }}</td>
                <td style="padding: 12px;">{{ $trx->kasir_name }}</td>
                <td style="padding: 12px; font-weight: 600;">Rp {{ number_format($trx->sub_total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding: 24px; text-align: center; color: #6b7280;">Tidak ada transaksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
