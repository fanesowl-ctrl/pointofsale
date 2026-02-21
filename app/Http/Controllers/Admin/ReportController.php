<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Menampilkan Data Laporan Bulanan (Auto-generated dari transaksi)
     */
    public function index(Request $request)
    {
        // Generate monthly reports from transactions
        $monthlyReports = $this->generateMonthlyReports();

        // Apply search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $monthlyReports = $monthlyReports->filter(function ($report) use ($search) {
                return stripos($report->month, $search) !== false;
            });
        }

        // Pagination
        $perPage = (int) $request->input('per_page', 10);
        $page = $request->input('page', 1);
        
        $reports = new \Illuminate\Pagination\LengthAwarePaginator(
            $monthlyReports->forPage($page, $perPage),
            $monthlyReports->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Chart Data (Last 12 Months)
        $chartData = $monthlyReports->take(12)->reverse();
        $chartLabels = $chartData->pluck('month_name');
        $chartSales = $chartData->pluck('total_sales');
        $chartProfit = $chartData->pluck('profit_loss');

        return view('admin.reports.index', compact('reports', 'chartLabels', 'chartSales', 'chartProfit'));
    }

    /**
     * Generate monthly reports from transactions
     */
    private function generateMonthlyReports()
    {
        // Get all unique year-month combinations from transactions
        $periods = Transaksi::select(
            DB::raw('YEAR(tanggal) as year'),
            DB::raw('MONTH(tanggal) as month')
        )
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        $reports = collect();

        foreach ($periods as $period) {
            $year = $period->year;
            $month = $period->month;

            // Count sold items (total quantity)
            $soldItems = TransaksiDetail::whereHas('transaksi', function ($query) use ($year, $month) {
                $query->whereYear('tanggal', $year)
                      ->whereMonth('tanggal', $month);
            })->sum('quantity');

            // Calculate total sales
            $totalSales = Transaksi::whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->sum('sub_total');

            // Calculate profit (selling_price - cost_price) * quantity
            $profit = TransaksiDetail::whereHas('transaksi', function ($query) use ($year, $month) {
                $query->whereYear('tanggal', $year)
                      ->whereMonth('tanggal', $month);
            })
            ->join('products', 'transaksi_details.product_id', '=', 'products.id')
            ->selectRaw('SUM((transaksi_details.price - products.cost_price) * transaksi_details.quantity) as total_profit')
            ->value('total_profit') ?? 0;

            $reports->push((object)[
                'id' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT),
                'year' => $year,
                'month' => $month,
                'month_name' => \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y'),
                'sold_items' => $soldItems,
                'total_sales' => $totalSales,
                'profit_loss' => $profit,
            ]);
        }

        return $reports;
    }

    /**
     * Show Detail Laporan (Daftar Transaksi Bulanan)
     */
    public function show($year, $month)
    {
        $transactions = Transaksi::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->with(['details.product'])
            ->orderBy('tanggal', 'desc')
            ->get();
        
        $period = \Carbon\Carbon::createFromDate($year, $month, 1);

        // Calculate summary
        $summary = (object)[
            'total_transactions' => $transactions->count(),
            'sold_items' => TransaksiDetail::whereHas('transaksi', function ($query) use ($year, $month) {
                $query->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
            })->sum('quantity'),
            'total_sales' => $transactions->sum('sub_total'),
            'profit' => TransaksiDetail::whereHas('transaksi', function ($query) use ($year, $month) {
                $query->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
            })
            ->join('products', 'transaksi_details.product_id', '=', 'products.id')
            ->selectRaw('SUM((transaksi_details.price - products.cost_price) * transaksi_details.quantity) as total_profit')
            ->value('total_profit') ?? 0,
        ];

        return view('admin.reports.show', compact('transactions', 'period', 'summary'));
    }

    /**
     * Export PDF (Print View)
     */
    public function exportPdf($year, $month)
    {
        $transactions = Transaksi::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->with(['details.product'])
            ->orderBy('tanggal', 'desc')
            ->get();
        
        $period = \Carbon\Carbon::createFromDate($year, $month, 1);

        $summary = (object)[
            'total_transactions' => $transactions->count(),
            'sold_items' => TransaksiDetail::whereHas('transaksi', function ($query) use ($year, $month) {
                $query->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
            })->sum('quantity'),
            'total_sales' => $transactions->sum('sub_total'),
            'profit' => TransaksiDetail::whereHas('transaksi', function ($query) use ($year, $month) {
                $query->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
            })
            ->join('products', 'transaksi_details.product_id', '=', 'products.id')
            ->selectRaw('SUM((transaksi_details.price - products.cost_price) * transaksi_details.quantity) as total_profit')
            ->value('total_profit') ?? 0,
        ];

        // Return print view instead of PDF
        return view('admin.reports.pdf', compact('transactions', 'period', 'summary'));
    }
}
