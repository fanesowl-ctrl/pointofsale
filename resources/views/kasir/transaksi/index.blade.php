@extends('layouts.kasir')

@section('title', 'Riwayat Transaksi')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h1 class="page-title" style="margin-bottom: 0;">Riwayat Transaksi</h1>
    <a href="{{ route('kasir.transaksi.create') }}" class="btn-primary" style="text-decoration: none; padding: 10px 20px; background: var(--primary-color); color: white; border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
        <i class="ri-add-line"></i> Transaksi Baru
    </a>
</div>

@if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <form action="{{ route('kasir.transaksi.index') }}" method="GET" style="display: flex; align-items: center; gap: 10px;">
            <label>Show</label>
            <select name="limit" onchange="this.form.submit()" style="padding: 6px; border-radius: 6px; border: 1px solid #d1d5db;">
                <option value="10" {{ request('limit') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('limit') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50</option>
            </select>
            <label>entries</label>
        </form>

        <form action="{{ route('kasir.transaksi.index') }}" method="GET">
            <input type="text" name="search" placeholder="Cari ID Transaksi..." value="{{ request('search') }}" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; width: 250px;">
        </form>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f9fafb; text-align: left;">
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">No</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">ID Transaksi</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Tanggal</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Sub Total</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $key => $trx)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px;">{{ $transactions->firstItem() + $key }}</td>
                <td style="padding: 12px; font-family: monospace;">{{ $trx->nomor_transaksi }}</td>
                <td style="padding: 12px;">{{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y') }}</td>
                <td style="padding: 12px; font-weight: 600;">Rp {{ number_format($trx->sub_total, 0, ',', '.') }}</td>
                <td style="padding: 12px;">
                    <a href="{{ route('kasir.transaksi.edit', $trx->id) }}" style="background: #f59e0b; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 4px;">
                        <i class="ri-pencil-line"></i>
                    </a>
                    
                    <form action="{{ route('kasir.transaksi.destroy', $trx->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer;">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding: 24px; text-align: center; color: #6b7280;">Belum ada transaksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $transactions->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
