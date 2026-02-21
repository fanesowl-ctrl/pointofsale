@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1 class="page-title">Dashboard Overview</h1>

    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid-3">
        <!-- Card 1: Total Sales -->
        <div class="card" style="border-top: 4px solid var(--primary-color);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 8px;">Total Penjualan</h3>
                    <div style="font-size: 1.8rem; font-weight: 700;">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                </div>
                <div style="background: var(--primary-light); color: var(--primary-color); padding: 10px; border-radius: 12px;">
                    <i class="ri-shopping-cart-2-line" style="font-size: 1.5rem;"></i>
                </div>
            </div>
            <div style="margin-top: 16px; font-size: 0.85rem; color: #10b981;">
                <i class="ri-arrow-up-line"></i> Total turnover
            </div>
        </div>

        <!-- Card 2: Total Products -->
        <div class="card" style="border-top: 4px solid #f59e0b;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 8px;">Total Produk</h3>
                    <div style="font-size: 1.8rem; font-weight: 700;">{{ $totalProducts }}</div>
                </div>
                <div style="background: #fef3c7; color: #d97706; padding: 10px; border-radius: 12px;">
                    <i class="ri-box-3-line" style="font-size: 1.5rem;"></i>
                </div>
            </div>
            <div style="margin-top: 16px; font-size: 0.85rem; color: var(--text-muted);">
                Item aktif di katalog
            </div>
        </div>

        <!-- Card 3: Customers -->
        <div class="card" style="border-top: 4px solid #10b981;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 8px;">Pelanggan</h3>
                    <div style="font-size: 1.8rem; font-weight: 700;">{{ $totalCustomers }}</div>
                </div>
                <div style="background: #d1fae5; color: #059669; padding: 10px; border-radius: 12px;">
                    <i class="ri-user-heart-line" style="font-size: 1.5rem;"></i>
                </div>
            </div>
            <div style="margin-top: 16px; font-size: 0.85rem; color: var(--text-muted);">
                Member terdaftar
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="margin-top: 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
        
        <!-- Grafik Barang Terlaris -->
        <div class="card" style="min-height: 400px;">
            <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px;">Barang Terlaris (Top 10)</h2>
            <div style="height: 300px; position: relative;">
                <canvas id="bestSellingChart"></canvas>
            </div>
        </div>

        <!-- Statistik Penjualan per Hari -->
        <div class="card" style="min-height: 400px;">
            <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px;">Penjualan Harian (30 Hari Terakhir)</h2>
            <div style="height: 300px; position: relative;">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>

        <!-- Statistik Penjualan per Jam -->
        <div class="card" style="min-height: 400px; grid-column: 1 / -1;">
            <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px;">Distribusi Penjualan per Jam (Peak Hours)</h2>
            <div style="height: 300px; position: relative;">
                <canvas id="hourlySalesChart"></canvas>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Best Selling Products Chart ---
        const ctxBestSelling = document.getElementById('bestSellingChart').getContext('2d');
        new Chart(ctxBestSelling, {
            type: 'bar',
            data: {
                labels: {!! json_encode($topProductsLabels) !!},
                datasets: [{
                    label: 'Terjual (Pcs)',
                    data: {!! json_encode($topProductsData) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Horizontal bar chart for better readability of names
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });

        // --- 2. Daily Sales Chart ---
        const ctxDaily = document.getElementById('dailySalesChart').getContext('2d');
        new Chart(ctxDaily, {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: {!! json_encode($dailyData) !!},
                    borderColor: 'rgba(16, 185, 129, 1)', // Emerald green
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    tension: 0.4, // Smooth curves
                    fill: true,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000) + 'k';
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // --- 3. Hourly Sales Chart ---
        const ctxHourly = document.getElementById('hourlySalesChart').getContext('2d');
        new Chart(ctxHourly, {
            type: 'bar',
            data: {
                labels: {!! json_encode($hourlyLabels) !!},
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: {!! json_encode($hourlyData) !!},
                    backgroundColor: 'rgba(245, 158, 11, 0.7)', // Amber
                    borderColor: 'rgba(245, 158, 11, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Jumlah Transaksi' }
                    },
                    x: {
                        title: { display: true, text: 'Jam' },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endpush
