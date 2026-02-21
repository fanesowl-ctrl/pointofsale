<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\LaporanHarian;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search');

        // Generate laporan dari transaksi yang ada
        $this->generateDailyReports();

        $laporan = LaporanHarian::when($search, function ($query, $search) {
            return $query->where('tanggal', 'like', "%{$search}%");
        })
        ->orderBy('tanggal', 'desc')
        ->paginate($limit);

        return view('kasir.laporan.index', compact('laporan'));
    }

    public function detail($tanggal)
    {
        $laporan = LaporanHarian::where('tanggal', $tanggal)->firstOrFail();
        
        // Get all transactions for this date
        $transaksi = Transaksi::whereDate('tanggal', $tanggal)
            ->with(['details.product'])
            ->get();

        return view('kasir.laporan.detail', compact('laporan', 'transaksi'));
    }

    public function exportPdf($tanggal)
    {
        $laporan = LaporanHarian::where('tanggal', $tanggal)->firstOrFail();
        
        $transaksi = Transaksi::whereDate('tanggal', $tanggal)
            ->with(['details.product'])
            ->get();

        // Return print view instead of PDF
        return view('kasir.laporan.pdf', compact('laporan', 'transaksi'));
    }

    /**
     * Generate daily reports from transactions
     */
    private function generateDailyReports()
    {
        // Get all unique transaction dates
        $dates = Transaksi::select(DB::raw('DATE(tanggal) as date'))
            ->groupBy('date')
            ->pluck('date');

        foreach ($dates as $date) {
            $totalTransaksi = Transaksi::whereDate('tanggal', $date)->count();
            
            $barangTerjual = TransaksiDetail::whereHas('transaksi', function ($query) use ($date) {
                $query->whereDate('tanggal', $date);
            })->sum('quantity');
            
            $totalPenjualan = Transaksi::whereDate('tanggal', $date)->sum('sub_total');

            LaporanHarian::updateOrCreate(
                ['tanggal' => $date],
                [
                    'total_transaksi' => $totalTransaksi,
                    'barang_terjual' => $barangTerjual,
                    'total_penjualan' => $totalPenjualan,
                ]
            );
        }
    }
}
