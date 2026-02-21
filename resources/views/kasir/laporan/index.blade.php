@extends('layouts.kasir')

@section('title', 'Laporan Harian')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h1 class="page-title" style="margin-bottom: 0;">Laporan Harian Penjualan</h1>
</div>

@if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <form action="{{ route('kasir.laporan.index') }}" method="GET" style="display: flex; align-items: center; gap: 10px;">
            <label>Show</label>
            <select name="limit" onchange="this.form.submit()" style="padding: 6px; border-radius: 6px; border: 1px solid #d1d5db;">
                <option value="10" {{ request('limit') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('limit') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50</option>
            </select>
            <label>entries</label>
        </form>

        <form action="{{ route('kasir.laporan.index') }}" method="GET">
            <input type="text" name="search" placeholder="Cari tanggal..." value="{{ request('search') }}" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; width: 250px;">
        </form>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f9fafb; text-align: left;">
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">No</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Tanggal Penjualan</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Barang Terjual</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Total Penjualan</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $key => $item)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px;">{{ $laporan->firstItem() + $key }}</td>
                <td style="padding: 12px;">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                <td style="padding: 12px;">{{ number_format($item->barang_terjual, 0, ',', '.') }} item</td>
                <td style="padding: 12px; font-weight: 600;">Rp {{ number_format($item->total_penjualan, 0, ',', '.') }}</td>
                <td style="padding: 12px;">
                    <a href="{{ route('kasir.laporan.detail', $item->tanggal) }}" style="background: #0ea5e9; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 4px;">
                        <i class="ri-eye-line"></i> Detail
                    </a>
                    
                    <a href="{{ route('kasir.laporan.pdf', $item->tanggal) }}" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block;">
                        <i class="ri-file-pdf-line"></i> PDF
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding: 24px; text-align: center; color: #6b7280;">Belum ada laporan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $laporan->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
