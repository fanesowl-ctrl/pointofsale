<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar barang (Index)
     */
    public function index(Request $request)
    {
        $query = DB::table('products');

        // Fitur Pencarian (Search)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        // Filter Kategori
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        // Fitur "Show" (Pagination)
        // Default tampilkan 10 jika tidak diset
        $perPage = (int) $request->input('per_page', 10);
        
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Get unique categories for filter dropdown
        $categories = DB::table('products')
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Menampilkan daftar barang diskon (Discounted Index)
     */
    public function discountedIndex(Request $request)
    {
        // Auto-Clear Expired Discounts (Otomatis hapus diskon jika waktu habis)
        DB::table('products')
            ->where('discount_end', '<', now())
            ->where('discount_percentage', '>', 0)
            ->update([
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'final_price' => DB::raw('selling_price'),
                'discount_stock' => null,
                'discount_start' => null,
                'discount_end' => null,
            ]);

        $query = DB::table('products')
            ->where('discount_percentage', '>', 0); // Filter hanya barang diskon

        // Fitur Pencarian (Search) untuk barang diskon
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        
        $products = $query->orderBy('discount_percentage', 'desc')->paginate($perPage);

        return view('admin.products.discounted', compact('products'));
    }

    /**
     * Menyimpan barang baru (Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_code' => 'required|unique:products,product_code',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_stock' => 'nullable|integer|min:0',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ], [
            'product_code.required' => 'Kode barang wajib diisi.',
            'product_code.unique' => 'Kode barang sudah ada.',
            'name.required' => 'Nama barang wajib diisi.',
            'cost_price.required' => 'Harga awal wajib diisi.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'discount_percentage.numeric' => 'Diskon harus berupa angka.',
            'discount_percentage.min' => 'Diskon minimal 0%.',
            'discount_percentage.max' => 'Diskon maksimal 100%.',
            'stock.required' => 'Stok wajib diisi.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $imagePath = null;
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('products', $imageName, 'public');
        }

        // Hitung diskon & stok diskon
        $discountPercentage = $request->discount_percentage ?? 0;
        $sellingPrice = $request->selling_price;
        $discountAmount = ($sellingPrice * $discountPercentage) / 100;
        $finalPrice = $sellingPrice - $discountAmount;
        
        // Logical check: If no discount, discount_stock and dates should be null
        $discountStock = ($discountPercentage > 0) ? $request->discount_stock : null;
        $discountStart = ($discountPercentage > 0) ? $request->discount_start : null;
        $discountEnd = ($discountPercentage > 0) ? $request->discount_end : null;

        DB::table('products')->insert([
            'product_code' => $request->product_code,
            'name' => $request->name,
            'category' => $request->category,
            'cost_price' => $request->cost_price,
            'selling_price' => $request->selling_price,
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
            'discount_stock' => $discountStock,
            'discount_start' => $discountStart,
            'discount_end' => $discountEnd,
            'stock' => $request->stock,
            'image' => $imagePath,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit (digunakan untuk modal/edit page)
     * Untuk kesederhanaan, kita bisa return JSON jika pakai modal, 
     * tapi di sini saya buat return view atau data untuk di-inject ke modal.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'product_code' => 'required|unique:products,product_code,'.$id,
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'cost_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_stock' => 'nullable|integer|min:0',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'discount_percentage.numeric' => 'Diskon harus berupa angka.',
            'discount_percentage.min' => 'Diskon minimal 0%.',
            'discount_percentage.max' => 'Diskon maksimal 100%.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        // Get Stock Before Update
        $oldProduct = DB::table('products')->where('id', $id)->first();
        $oldStock = $oldProduct->stock;
        $newStock = $request->stock;

        // Hitung diskon
        $discountPercentage = $request->discount_percentage ?? 0;
        $sellingPrice = $request->selling_price;
        $discountAmount = ($sellingPrice * $discountPercentage) / 100;
        $finalPrice = $sellingPrice - $discountAmount;
        
        $discountStock = ($discountPercentage > 0) ? $request->discount_stock : null;
        $discountStart = ($discountPercentage > 0) ? $request->discount_start : null;
        $discountEnd = ($discountPercentage > 0) ? $request->discount_end : null;

        $updateData = [
            'product_code' => $request->product_code,
            'name' => $request->name,
            'category' => $request->category,
            'cost_price' => $request->cost_price,
            'selling_price' => $request->selling_price,
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
            'discount_stock' => $discountStock,
            'discount_start' => $discountStart,
            'discount_end' => $discountEnd,
            'stock' => $request->stock,
            'updated_at' => \Carbon\Carbon::now(),
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($oldProduct && $oldProduct->image) {
                Storage::disk('public')->delete($oldProduct->image);
            }
            
            // Store new image
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            
            $updateData['image'] = $imagePath;
        }

        DB::table('products')->where('id', $id)->update($updateData);

        // FIFO Auto Adjustment Logic
        if ($oldStock != $newStock) {
            $diff = $newStock - $oldStock;
            
            if ($diff > 0) {
                 // Penambahan Stok -> Buat Batch Baru
                 \App\Models\StockBatch::create([
                    'product_id' => $id,
                    'quantity' => $diff,
                    'original_quantity' => $diff,
                    'cost_price' => $request->cost_price, // Pakai harga baru
                    'received_at' => \Carbon\Carbon::now(),
                    'batch_code' => 'AUTO-ADJUST-' . time(),
                 ]);
            } else {
                 // Pengurangan Stok -> Kurangi dari FIFO (Manual Loop agar tidak double update stock)
                 $qtyToReduce = abs($diff);
                 
                 // Ambil batches FIFO
                 $batches = \App\Models\StockBatch::where('product_id', $id)
                    ->where('quantity', '>', 0)
                    ->orderBy('received_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
                    
                 foreach ($batches as $batch) {
                    if ($qtyToReduce <= 0) break;
                    $take = min($batch->quantity, $qtyToReduce);
                    
                    $batch->quantity -= $take;
                    $batch->save();
                    
                    $qtyToReduce -= $take;
                 }
            }
        }
        
        $redirectRoute = $request->input('redirect_to') === 'discounted' 
            ? 'admin.products.discounted' 
            : 'admin.products.index';

        return redirect()->route($redirectRoute)->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Menghapus barang
     */
    public function destroy($id)
    {
        try {
            DB::table('products')->where('id', $id)->delete();
            return redirect()->route('admin.products.index')->with('success', 'Barang berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.products.index')->withErrors(['error' => 'Barang tidak bisa dihapus karena sudah pernah ditransaksikan.']);
        }
    }

    /**
     * Export data ke Excel (CSV)
     */
    public function export()
    {
        $fileName = 'data-barang-'.date('Y-m-d').'.csv';
        $products = DB::table('products')->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('No', 'Kode Barang', 'Nama Barang', 'Harga Awal', 'Harga Jual', 'Diskon (%)', 'Harga Setelah Diskon', 'Stok', 'Tanggal Dibuat');

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $key => $product) {
                $row['No']  = $key + 1;
                $row['Kode Barang']    = $product->product_code;
                $row['Nama Barang']    = $product->name;
                $row['Harga Awal']  = $product->cost_price;
                $row['Harga Jual']  = $product->selling_price;
                $row['Diskon (%)']  = $product->discount_percentage ?? 0;
                $row['Harga Setelah Diskon']  = $product->final_price ?? $product->selling_price;
                $row['Stok']  = $product->stock;
                $row['Tanggal Dibuat'] = $product->created_at;

                fputcsv($file, array($row['No'], $row['Kode Barang'], $row['Nama Barang'], $row['Harga Awal'], $row['Harga Jual'], $row['Diskon (%)'], $row['Harga Setelah Diskon'], $row['Stok'], $row['Tanggal Dibuat']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
