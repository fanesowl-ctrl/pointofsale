@extends('layouts.admin')

@section('title', 'Detail Laporan')

@section('content')
<div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: start;">
    <div>
        <a href="{{ route('admin.reports.index') }}" style="text-decoration: none; color: var(--text-muted); display: inline-flex; align-items: center; gap: 5px; font-weight: 500; font-size: 0.9rem;">
            <i class="ri-arrow-left-line"></i> Kembali ke Laporan Bulanan
        </a>
        <h1 class="page-title" style="margin-top: 10px; margin-bottom: 5px;">
            Laporan: {{ $period->locale('id')->isoFormat('MMMM Y') }}
        </h1>
        <div style="color: var(--text-muted); font-size: 0.9rem;">
            Detail semua transaksi yang terjadi pada bulan ini
        </div>
    </div>
    
    <a href="{{ route('admin.reports.pdf', ['year' => $period->year, 'month' => $period->month]) }}" target="_blank"
       style="background: #ef4444; color: white; border: none; padding: 10px 16px; border-radius: 8px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 500;">
        <i class="ri-printer-line"></i> Cetak Laporan
    </a>
</div>

<!-- Summary Cards for Specific Month -->
<div class="grid-4" style="margin-bottom: 24px;">
    <div class="card" style="padding: 16px; border-left: 4px solid var(--primary-color);">
        <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 5px;">Total Transaksi</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ number_format($summary->total_transactions, 0, ',', '.') }}</div>
    </div>
    <div class="card" style="padding: 16px; border-left: 4px solid #f59e0b;">
        <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 5px;">Item Terjual</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ number_format($summary->sold_items, 0, ',', '.') }}</div>
    </div>
    <div class="card" style="padding: 16px; border-left: 4px solid #10b981;">
        <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 5px;">Total Omset</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: #059669;">Rp {{ number_format($summary->total_sales, 0, ',', '.') }}</div>
    </div>
    <div class="card" style="padding: 16px; border-left: 4px solid #6366f1;">
        <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 5px;">Laba Bersih</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: #4338ca;">Rp {{ number_format($summary->profit, 0, ',', '.') }}</div>
    </div>
</div>

<div class="card">
    <div style="margin-bottom: 16px;">
        <h3 style="font-size: 1.1rem; font-weight: 600;">Daftar Transaksi</h3>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead>
                <tr style="background: #f9fafb; text-align: left;">
                    <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; width: 50px;">No</th>
                    <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; width: 180px;">Waktu & ID</th>
                    <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Detail Item</th>
                    <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; width: 120px;">Metode</th>
                    <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; width: 150px; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $key => $txn)
                <tr style="border-bottom: 1px solid #f3f4f6; vertical-align: top;">
                    <td style="padding: 12px; color: var(--text-muted);">{{ $key + 1 }}</td>
                    <td style="padding: 12px;">
                        <div style="font-weight: 500; color: #1f2937;">{{ $txn->created_at->format('d M H:i') }}</div>
                        <div style="font-size: 0.8rem; color: #6b7280; margin-top: 4px; font-family: monospace;">{{ $txn->nomor_transaksi }}</div>
                        <div style="font-size: 0.8rem; color: #6b7280; margin-top: 2px;">
                            <i class="ri-user-line"></i> {{ $txn->kasir_name ?? 'Admin' }}
                        </div>
                    </td>
                    <td style="padding: 12px;">
                        <ul style="margin: 0; padding-left: 20px; font-size: 0.9rem; color: #374151;">
                            @foreach($txn->details as $detail)
                                <li style="margin-bottom: 4px;">
                                    <span style="font-weight: 500;">{{ $detail->product->name ?? 'Produk Dihapus' }}</span> 
                                    <span style="color: #6b7280;">({{ $detail->quantity }}x @ Rp {{ number_format($detail->price, 0, ',', '.') }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td style="padding: 12px;">
                        @if($txn->payment_method == 'qris')
                            <span style="background: #e0e7ff; color: #4338ca; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">QRIS</span>
                        @else
                            <span style="background: #f3f4f6; color: #374151; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">TUNAI</span>
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: right; font-weight: 600; color: #111827;">
                        Rp {{ number_format($txn->sub_total, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 32px; text-align: center; color: #6b7280;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                            <i class="ri-file-search-line" style="font-size: 2rem; color: #d1d5db;"></i>
                            <p>Belum ada data transaksi di bulan ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
