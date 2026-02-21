@extends('layouts.admin')

@section('title', 'Laporan Bulanan')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="margin-bottom: 5px;">Laporan Bulanan</h1>
        <div style="color: var(--text-muted); font-size: 0.9rem;">
            Ringkasan kinerja keuangan bulanan
        </div>
    </div>
    
    <!-- Optional: Add Year Filter here if needed later -->
</div>

@if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<!-- Sales Trend Chart -->
<div class="card" style="margin-bottom: 24px;">
    <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px;">Tren Penjualan & Laba (12 Bulan Terakhir)</h3>
    <div style="height: 350px;">
        <canvas id="trendChart"></canvas>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <form action="{{ route('admin.reports.index') }}" method="GET" style="display: flex; align-items: center; gap: 10px;">
            <label style="font-size: 0.9rem; color: var(--text-muted);">Tampilkan</label>
            <select name="per_page" onchange="this.form.submit()" style="padding: 8px; border-radius: 6px; border: 1px solid #d1d5db; background-color: #f9fafb;">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            </select>
        </form>

        <form action="{{ route('admin.reports.index') }}" method="GET" style="position: relative;">
            <i class="ri-search-line" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
            <input type="text" name="search" placeholder="Cari nama bulan..." value="{{ request('search') }}" 
                   style="padding: 8px 12px 8px 36px; border: 1px solid #d1d5db; border-radius: 6px; width: 250px; outline: none; transition: all 0.2s;">
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr style="background: #f9fafb; text-align: left;">
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); border-bottom: 1px solid #e5e7eb;">Bulan</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); border-bottom: 1px solid #e5e7eb;">Item Terjual</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); border-bottom: 1px solid #e5e7eb;">Total Omset</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); border-bottom: 1px solid #e5e7eb;">Laba Bersih</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); border-bottom: 1px solid #e5e7eb; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                <tr style="transition: background 0.2s;">
                    <td style="padding: 16px; border-bottom: 1px solid #f3f4f6; font-weight: 500;">
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <i class="ri-calendar-event-line" style="color: var(--primary-color);"></i>
                            {{ $report->month_name }}
                        </span>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f3f4f6;">
                        {{ number_format($report->sold_items, 0, ',', '.') }} pcs
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f3f4f6;">
                        <span style="font-weight: 600; color: #1f2937;">Rp {{ number_format($report->total_sales, 0, ',', '.') }}</span>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f3f4f6;">
                        <span style="background: {{ $report->profit_loss >= 0 ? '#d1fae5' : '#fee2e2' }}; color: {{ $report->profit_loss >= 0 ? '#059669' : '#b91c1c' }}; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 500;">
                            {{ $report->profit_loss >= 0 ? '+' : '' }} Rp {{ number_format($report->profit_loss, 0, ',', '.') }}
                        </span>
                    </td>
                    <td style="padding: 16px; border-bottom: 1px solid #f3f4f6; text-align: right;">
                        <a href="{{ route('admin.reports.show', ['year' => $report->year, 'month' => $report->month]) }}" 
                           class="btn-action" style="margin-right: 4px; background: #eff6ff; color: #3b82f6;" title="Detail">
                            <i class="ri-eye-line"></i>
                        </a>
                        <a href="{{ route('admin.reports.pdf', ['year' => $report->year, 'month' => $report->month]) }}" target="_blank"
                           class="btn-action" style="background: #fef2f2; color: #ef4444;" title="Export PDF">
                            <i class="ri-file-pdf-line"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: #6b7280;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                            <i class="ri-inbox-archive-line" style="font-size: 2rem; color: #d1d5db;"></i>
                            <p>Belum ada data laporan transaksi.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $reports->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>

<style>
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-action:hover {
        filter: brightness(0.95);
        transform: translateY(-1px);
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('trendChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Total Penjualan (RP)',
                        data: {!! json_encode($chartSales) !!},
                        borderColor: '#3b82f6', // Blue
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Laba Bersih (RP)',
                        data: {!! json_encode($chartProfit) !!},
                        borderColor: '#10b981', // Emerald
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.3,
                        fill: false,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value/1000) + 'k';
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    });
</script>
@endpush
