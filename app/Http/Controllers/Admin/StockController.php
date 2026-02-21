<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockBatch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockController extends Controller
{
    /**
     * Inisialisasi Data Stok Lama ke Sistem FIFO (Migration)
     * Dipanggil sekali jika tabel kosong.
     */
    private function checkAndMigrateStocks()
    {
        if (StockBatch::count() == 0) {
            $products = Product::where('stock', '>', 0)->get();
            foreach ($products as $product) {
                StockBatch::create([
                    'product_id' => $product->id,
                    'quantity' => $product->stock,
                    'original_quantity' => $product->stock,
                    'cost_price' => $product->cost_price,
                    'received_at' => Carbon::now()->subDay(), // Anggap stok lama sudah ada kemarin
                    'expiry_date' => null, // Tidak diketahui
                    'batch_code' => 'INITIAL-STOCK-' . date('Ymd'),
                ]);
            }
        }
    }

    public function index(Request $request)
    {
        $this->checkAndMigrateStocks(); // Pastikan data awal ada

        $query = StockBatch::with('product')
            ->where('quantity', '>', 0) // Tampilkan hanya batch aktif
            ->orderBy('expiry_date', 'asc') // Urutkan berdasarkan Expired terdekat (FEFO) atau Masuk terlama (FIFO)
            ->orderBy('received_at', 'asc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            });
        }
        
        // Filter by Status Expired
        if ($request->has('status')) {
            if ($request->status == 'expired') {
                $query->where('expiry_date', '<', Carbon::now());
            } elseif ($request->status == 'warning') {
                $query->whereBetween('expiry_date', [Carbon::now(), Carbon::now()->addDays(30)]);
            }
        }

        $batches = $query->paginate(20);

        return view('admin.stocks.index', compact('batches'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('admin.stocks.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'cost_price' => 'nullable|numeric|min:0',
            'received_at' => 'required|date',
            'expiry_date' => 'nullable|date|after:received_at',
            'batch_code' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();
            
            $product = Product::findOrFail($request->product_id);
            
            // Simpan ke detail batch (FIFO record)
            StockBatch::create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'original_quantity' => $request->quantity,
                'cost_price' => $request->cost_price ?? $product->cost_price,
                'received_at' => $request->received_at,
                'expiry_date' => $request->expiry_date,
                'batch_code' => $request->batch_code ?? ('BATCH-' . time()),
            ]);

            // Update Stok Agregat Display
            // Sebaiknya, update harga modal jika user input harga baru?
            // Biasanya harga modal dirata-rata (Average Cost) atau FIFO cost.
            // Untuk simple POS ini, kita biarkan cost_price di product tetap yg lama atau update manual.
            
            $product->increment('stock', $request->quantity);

            DB::commit();

            return redirect()->route('admin.stocks.index')->with('success', 'Stok berhasil ditambahkan ke dalam antrian FIFO.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menambah stok: ' . $e->getMessage()]);
        }
    }
    
    // Fitur Edit Batch (Koreksi Stok / Expired Date)
    public function edit($id)
    {
        $batch = StockBatch::with('product')->findOrFail($id);
        return view('admin.stocks.edit', compact('batch'));
    }

    public function update(Request $request, $id)
    {
         $request->validate([
            'expiry_date' => 'nullable|date',
            'batch_code' => 'nullable|string|max:50',
            'quantity' => 'required|integer|min:0', // Bisa adjust quantity jika salah input (opname)
        ]);
        
        try {
            DB::beginTransaction();
            $batch = StockBatch::findOrFail($id);
            $oldQty = $batch->quantity;
            $newQty = $request->quantity;
            
            $batch->update([
                'expiry_date' => $request->expiry_date,
                'batch_code' => $request->batch_code,
                'quantity' => $newQty,
            ]);
            
            // Sync Product Total Stock
            $diff = $newQty - $oldQty;
            if ($diff != 0) {
                $product = Product::findOrFail($batch->product_id);
                if ($diff > 0) {
                    $product->increment('stock', $diff);
                } else {
                    $product->decrement('stock', abs($diff));
                }
            }
            
            DB::commit();
            return redirect()->route('admin.stocks.index')->with('success', 'Data batch berhasil diperbarui.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal update batch: ' . $e->getMessage()]);
        }
    }
}
