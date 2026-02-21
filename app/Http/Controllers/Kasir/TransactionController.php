<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Product;
use App\Models\LaporanHarian;
use App\Models\Member; // Add Member Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = $request->input('search');

        $transactions = Transaksi::when($search, function ($query, $search) {
            return $query->where('nomor_transaksi', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate($limit);

        return view('kasir.transaksi.index', compact('transactions'));
    }

    public function create()
    {
        // Auto-Clear Expired Discounts (Otomatis hapus diskon jika waktu habis)
        Product::where('discount_end', '<', now())
            ->where('discount_percentage', '>', 0)
            ->update([
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'final_price' => DB::raw('selling_price'),
                'discount_stock' => null,
                'discount_start' => null,
                'discount_end' => null,
            ]);

        $products = Product::where('stock', '>', 0)->get();
        $qrisImage = DB::table('payment_settings')->where('setting_key', 'qris_image')->value('setting_value');
        $recentTransactions = Transaksi::orderBy('created_at', 'desc')->limit(10)->get();
        return view('kasir.transaksi.create', compact('products', 'qrisImage', 'recentTransactions'));
    }

    public function checkMember(Request $request)
    {
        $request->validate([
            'member_code' => 'required|string',
        ]);

        $member = Member::where('code', $request->member_code)
            ->where('is_active', true)
            ->first();

        if ($member) {
            return response()->json([
                'success' => true,
                'member' => $member,
                'message' => 'Member valid: ' . $member->name . ' (Diskon ' . ($member->discount_percentage + 0) . '%)'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kode member tidak ditemukan atau tidak aktif.'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string|in:cash,qris',
        ]);

        try {
            DB::beginTransaction();

            $totalPrice = 0;
            $itemsToProcess = [];

            // Pre-calculate and validate
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                // Determine effective price based on discount availability
                $effectivePrice = $product->selling_price;
                
                // Cek Validitas Waktu Diskon
                $now = now();
                $discountStart = $product->discount_start;
                $discountEnd = $product->discount_end;
                
                $isTimeValid = true;
                if ($discountStart && $now->lt($discountStart)) $isTimeValid = false;
                if ($discountEnd && $now->gt($discountEnd)) $isTimeValid = false;

                // Cek apakah diskon aktif dan kuota mencukupi (atau unlimited)
                $isDiscountActive = false;
                if ($product->discount_percentage > 0 && $isTimeValid) {
                    if (is_null($product->discount_stock) || $product->discount_stock > 0) {
                        $effectivePrice = $product->final_price > 0 ? $product->final_price : ($product->selling_price - $product->discount_amount);
                        $isDiscountActive = true;
                    }
                }

                $qty = $item['quantity'];
                $totalPrice += $effectivePrice * $qty;
                
                // Check main stock
                if ($product->stock < $qty) {
                    throw new \Exception("Stok barang {$product->name} tidak mencukupi.");
                }

                $itemsToProcess[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'price' => $effectivePrice,
                    'is_discount_active' => $isDiscountActive
                ];
            }

            // Calculate Member Discount
            $discountAmount = 0;
            $memberId = null;
            
            if ($request->filled('member_code')) {
                $member = Member::where('code', $request->member_code)
                    ->where('is_active', true)
                    ->first();
                
                if ($member) {
                    $memberId = $member->id;
                    $discountAmount = ($totalPrice * $member->discount_percentage) / 100;
                }
            }
            
            $grandTotal = $totalPrice - $discountAmount;

            $transaksi = Transaksi::create([
                'nomor_transaksi' => 'TRX-' . time() . '-' . Str::random(4),
                'tanggal' => now(),
                'sub_total' => $totalPrice, 
                'discount_amount' => $discountAmount,
                'grand_total' => $grandTotal,
                'member_id' => $memberId,
                'kasir_name' => session('kasir_name', 'Kasir'),
                'payment_method' => $request->payment_method,
            ]);

            foreach ($itemsToProcess as $data) {
                $product = $data['product'];
                $qty = $data['quantity'];
                $price = $data['price'];
                $isDiscountActive = $data['is_discount_active'];

                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'total' => $price * $qty,
                ]);

                // Update Main Stock using FIFO
                $product->reduceStockFifo($qty);

                // Update Discount Stock (Quota) logic
                if ($isDiscountActive && !is_null($product->discount_stock)) {
                    // Reduce discount quota
                    $newDiscountStock = $product->discount_stock - $qty;
                    
                    // Logic: If quota runs out (<= 0), remove discount from product
                    if ($newDiscountStock <= 0) {
                        $product->discount_percentage = 0;
                        $product->discount_amount = 0;
                        $product->final_price = $product->selling_price;
                        $product->discount_stock = null; // Clear quota
                    } else {
                        $product->discount_stock = $newDiscountStock;
                    }
                    $product->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Transaksi berhasil disimpan!', 
                'redirect' => route('kasir.transaksi.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['details.product'])->findOrFail($id);
        return view('kasir.transaksi.show', compact('transaksi'));
    }

    public function edit($id)
    {
        $transaksi = Transaksi::with(['details.product'])->findOrFail($id);
        $products = Product::all();
        return view('kasir.transaksi.edit', compact('transaksi', 'products'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $transaksi = Transaksi::findOrFail($id);
            
            // Restore stock from old transaction
            foreach ($transaksi->details as $detail) {
                $detail->product->increment('stock', $detail->quantity);
            }
            
            // Delete old details
            $transaksi->details()->delete();

            // Calculate new total and create new details
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $totalPrice += $product->selling_price * $item['quantity'];
                
                // Check stock
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok barang {$product->name} tidak mencukupi.");
                }
            }

            // Update transaction
            $transaksi->update([
                'sub_total' => $totalPrice,
                'tanggal' => now(),
            ]);

            // Create new details and update stock
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->selling_price,
                    'total' => $product->selling_price * $item['quantity'],
                ]);

                $product->reduceStockFifo($item['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Transaksi berhasil diupdate!', 
                'redirect' => route('kasir.transaksi.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $transaksi = Transaksi::with('details.product')->findOrFail($id);
            
            // Restore stock
            foreach ($transaksi->details as $detail) {
                if ($detail->product) {
                    $detail->product->increment('stock', $detail->quantity);
                }
            }
            
            // Delete details manual (safeguard if cascade is missing in DB)
            $transaksi->details()->delete();

            // Delete transaction
            $tanggal = $transaksi->tanggal;
            $transaksi->delete();

            // Cek report harian, jika kosong hapus
            $count = Transaksi::whereDate('tanggal', $tanggal)->count();
            if ($count == 0) {
                LaporanHarian::whereDate('tanggal', $tanggal)->delete();
            }
            
            DB::commit();

            return redirect()->route('kasir.transaksi.index')->with('success', 'Transaksi berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}
