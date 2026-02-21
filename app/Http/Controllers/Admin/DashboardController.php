<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Grafik Barang yang Laku (Top 10)
        $topProducts = TransaksiDetail::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->with(['product' => function($query) {
                $query->select('id', 'name');
            }])
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
        
        // Prepare data for Chart.js
        $topProductsLabels = $topProducts->map(function($item) {
            return $item->product ? $item->product->name : 'Unknown';
        });
        $topProductsData = $topProducts->pluck('total_quantity');

        // 2. Statistik Penjualan per Hari (Last 30 Days)
        $startDate = Carbon::now()->subDays(29);
        $salesPerDay = Transaksi::select('tanggal', DB::raw('SUM(sub_total) as total_sales'))
            ->where('tanggal', '>=', $startDate)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Fill missing days with 0
        $dailyLabels = [];
        $dailyData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dailyLabels[] = Carbon::now()->subDays($i)->format('d M');
            
            $sale = $salesPerDay->firstWhere('tanggal', $date);
            $dailyData[] = $sale ? $sale->total_sales : 0;
        }

        // 3. Statistik Penjualan per Jam (Peak Hours - derived from created_at)
        // Group by hour of the day (0-23)
        $salesPerHour = Transaksi::select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as total_transactions'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
            
        $hourlyLabels = [];
        $hourlyData = [];
        
        // Initialize 0-23
        for ($i = 0; $i < 24; $i++) {
            $hourlyLabels[] = sprintf('%02d:00', $i);
            $found = $salesPerHour->firstWhere('hour', $i);
            $hourlyData[] = $found ? $found->total_transactions : 0;
        }

        // Summary Cards Data
        $totalSales = Transaksi::sum('sub_total');
        $totalProducts = \App\Models\Product::count();
        // Assuming no customer table yet, so 0 for now
        $totalCustomers = 0;

        return view('admin.dashboard', compact(
            'topProductsLabels', 
            'topProductsData', 
            'dailyLabels', 
            'dailyData', 
            'hourlyLabels', 
            'hourlyData',
            'totalSales',
            'totalProducts',
            'totalCustomers'
        ));
    }
}
