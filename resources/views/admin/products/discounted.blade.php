@extends('layouts.admin')

@section('title', 'Barang Diskon')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h1 class="page-title" style="margin-bottom: 0;">Barang Diskon</h1>
    <!-- Tombol Export Excel tetap ada jika diperlukan, atau hapus tombol tambah barang -->
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.products.export') }}" class="btn-secondary" style="text-decoration: none; padding: 10px 20px; background: #10b981; color: white; border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="ri-file-excel-2-line"></i> Export Excel
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <form action="{{ route('admin.products.discounted') }}" method="GET" style="display: flex; align-items: center; gap: 10px;">
            <label>Show</label>
            <select name="per_page" onchange="this.form.submit()" style="padding: 6px; border-radius: 6px; border: 1px solid #d1d5db;">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            </select>
            <label>entries</label>
        </form>

        <form action="{{ route('admin.products.discounted') }}" method="GET">
            <input type="text" name="search" placeholder="Cari barang diskon..." value="{{ request('search') }}" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; width: 250px;">
        </form>
    </div>
</div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px;">
        @forelse($products as $product)
        <div style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: transform 0.2s, box-shadow 0.2s; position: relative;">
            <!-- Product Image -->
            <div style="height: 200px; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                @if(isset($product->image) && $product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" 
                         alt="{{ $product->name }}" 
                         style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <div style="font-size: 3rem; color: #9ca3af;">
                        <i class="ri-shopping-bag-3-line"></i>
                    </div>
                @endif
                <!-- Discount Badge on Image -->
                @if(isset($product->discount_percentage) && $product->discount_percentage > 0)
                    <div style="position: absolute; top: 12px; left: 12px;">
                        <span style="padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);">
                            <i class="ri-price-tag-3-fill"></i> {{ number_format($product->discount_percentage, 0) }}% OFF
                        </span>
                    </div>
                @endif
                <!-- Stock Badge on Image -->
                <div style="position: absolute; top: 12px; right: 12px;">
                    @if(isset($product->discount_stock) && $product->discount_stock !== null)
                         <span style="padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: rgba(254, 226, 226, 0.9); color: #991b1b; backdrop-filter: blur(4px);">
                            Sisa Promo: {{ $product->discount_stock }}
                        </span>
                    @else
                        <!-- Jika unlimited, tampilkan stok biasa -->
                        @if($product->stock > 5)
                            <span style="padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: rgba(209, 250, 229, 0.9); color: #065f46; backdrop-filter: blur(4px);">
                                Stok: {{ $product->stock }}
                            </span>
                        @else
                            <span style="padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: rgba(254, 226, 226, 0.9); color: #991b1b; backdrop-filter: blur(4px);">
                                Sisa: {{ $product->stock }}
                            </span>
                        @endif
                    @endif
                </div>
            </div>

            <div style="padding: 20px;">
                <div style="margin-bottom: 8px;">
                     <span style="font-size: 0.85rem; color: #6b7280; background: #f3f4f6; padding: 2px 8px; border-radius: 4px;">{{ $product->product_code }}</span>
                </div>
                <h3 style="margin: 0 0 12px 0; font-size: 1.1rem; font-weight: 600; color: #1f2937; line-height: 1.4; height: 2.8em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical;">
                    {{ $product->name }}
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px;">
                    @if(isset($product->discount_percentage) && $product->discount_percentage > 0)
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #6b7280; font-size: 0.85rem;">Harga Normal</span>
                            <span style="color: #9ca3af; font-size: 0.9rem; text-decoration: line-through;">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #dc2626; font-size: 0.9rem; font-weight: 600;">Harga Diskon</span>
                            <span style="font-weight: 700; color: #dc2626; font-size: 1.2rem;">Rp {{ number_format($product->final_price ?? $product->selling_price, 0, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 10px; background: #fef2f2; border-radius: 6px; margin-top: 4px;">
                            <span style="color: #991b1b; font-size: 0.8rem; font-weight: 500;">Hemat</span>
                            <span style="color: #991b1b; font-size: 0.85rem; font-weight: 600;">Rp {{ number_format($product->discount_amount ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @else
                        <!-- Seharusnya tidak muncul di sini tapi untuk fallback -->
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #6b7280; font-size: 0.9rem;">Harga Jual</span>
                            <span style="font-weight: 700; color: #059669; font-size: 1.1rem;">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                        <span style="color: #6b7280; font-size: 0.85rem;">Harga Modal</span>
                        <span style="color: #9ca3af; font-size: 0.85rem;">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</span>
                    </div>

                    <!-- Info Waktu Diskon -->
                    <div style="margin-top: 10px; font-size: 0.75rem; color: #4b5563; background: #f8fafc; padding: 8px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                            <i class="ri-calendar-check-line" style="color: #059669;"></i> 
                            <span>Mulai: 
                                @if($product->discount_start)
                                    <strong>{{ \Carbon\Carbon::parse($product->discount_start)->format('d M Y H:i:s') }}</strong>
                                @else
                                    <span style="color: #059669; font-weight: 600;">Sekarang</span>
                                @endif
                            </span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i class="ri-hourglass-line" style="color: #dc2626;"></i> 
                            <span>Selesai: 
                                @if($product->discount_end)
                                    <strong>{{ \Carbon\Carbon::parse($product->discount_end)->format('d M Y H:i:s') }}</strong>
                                @else
                                    <span style="color: #059669; font-weight: 600;">Selamanya (Tidak Terbatas)</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick='editProduct(@json($product))' 
                        style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px; padding: 10px; background: #fef3c7; color: #d97706; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; transition: background 0.2s;">
                        <i class="ri-pencil-line"></i> Edit Diskon
                    </button>
                    <!-- Hapus tombol delete di sini agar fokus ke edit diskon, atau biarkan -->
                     <button type="button" onclick='editProduct(@json($product)); document.getElementById("edit_discount_percentage").value=0; document.getElementById("edit_discount_stock").value=""; toggleDiscountStock("edit");' 
                        style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px; padding: 10px; background: #fee2e2; color: #dc2626; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; transition: background 0.2s;">
                         <i class="ri-close-circle-line"></i> Reset Diskon
                     </button>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 48px; background: white; border-radius: 12px; color: #6b7280;">
            <i class="ri-price-tag-3-line" style="font-size: 3rem; margin-bottom: 16px; display: block; color: #ef4444;"></i>
            <p>Tidak ada barang yang sedang didiskon saat ini.</p>
        </div>
        @endforelse
    </div>

    <div class="card" style="padding: 16px; margin-bottom: 24px;">
        {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
    </div>

<!-- Modal Edit Barang (Fokus ke Diskon, tapi tetap bisa edit semua agar fleksibel) -->
<div id="editModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100; justify-content: center; align-items: center; overflow-y: auto;">
    <div class="modal-content" style="background: white; width: 500px; max-width: 90%; padding: 24px; border-radius: 16px; max-height: 90vh; overflow-y: auto; margin: 20px auto;">
        <h2 style="margin-bottom: 20px; font-size: 1.5rem;">Edit Diskon Barang</h2>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="redirect_to" value="discounted">
            
            <!-- Hidden Fields for required validation but untouced -->
            <input type="hidden" id="edit_product_code" name="product_code">
            <input type="hidden" id="edit_name" name="name">
            <input type="hidden" id="edit_cost_price" name="cost_price">
            <input type="hidden" id="edit_selling_price" name="selling_price">
            <input type="hidden" id="edit_stock" name="stock">
            
            <!-- Info Produk Readonly -->
            <div style="background: #f3f4f6; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                <h3 id="display_name" style="margin: 0 0 8px 0;">Product Name</h3>
                <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">Harga Jual: <span id="display_price" style="color: #1f2937; font-weight: 600;">Rp 0</span></p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Diskon (%) <span style="color: #dc2626;">*</span></label>
                    <input type="number" id="edit_discount_percentage" name="discount_percentage" min="0" max="100" step="0.01" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" placeholder="0-100" oninput="toggleDiscountStock('edit')" required>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Stok Promo</label>
                    <input type="number" id="edit_discount_stock" name="discount_stock" min="0" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" placeholder="Jumlah Promo">
                    <small style="color: #6b7280; font-size: 0.75rem;">Kosongkan jika unlimited</small>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Mulai Diskon (Termasuk Jam:Menit:Detik)</label>
                    <input type="datetime-local" id="edit_discount_start" name="discount_start" step="1" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
                     <small style="color: #6b7280; font-size: 0.75rem;">Kosongkan jika berlaku sekarang</small>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Berakhir Diskon (Termasuk Jam:Menit:Detik)</label>
                    <input type="datetime-local" id="edit_discount_end" name="discount_end" step="1" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
                     <small style="color: #6b7280; font-size: 0.75rem;">Kosongkan jika berlaku selamanya</small>
                </div>
            </div>
            
            <!-- Hidden Image Input if needed to preserve image logic? 
                 Actually, ProductController::update requires fields or not?
                 Controller validation: 'product_code' => 'required', 'name' => 'required', etc.
                 So I MUST send them. The hidden inputs above handle this.
            -->

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('editModal')" style="padding: 10px 20px; background: #e5e7eb; border: none; border-radius: 8px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'flex';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'none';
}

function editProduct(product) {
    // Fill hidden inputs
    document.getElementById('edit_product_code').value = product.product_code;
    document.getElementById('edit_name').value = product.name;
    document.getElementById('edit_cost_price').value = Math.floor(product.cost_price);
    document.getElementById('edit_selling_price').value = Math.floor(product.selling_price);
    document.getElementById('edit_stock').value = product.stock;

    // Fill Display info
    document.getElementById('display_name').innerText = product.name;
    document.getElementById('display_price').innerText = 'Rp ' + Number(product.selling_price).toLocaleString('id-ID');

    // Fill Discount Inputs
    document.getElementById('edit_discount_percentage').value = product.discount_percentage || '';
    document.getElementById('edit_discount_stock').value = product.discount_stock || '';
    
    // Fill Discount Date Inputs
    // Format: YYYY-MM-DDTHH:mm:ss
    if (product.discount_start) {
        let startVal = product.discount_start.replace(' ', 'T');
        // Pastikan format lengkap YYYY-MM-DDTHH:mm:ss (19 karakter)
        if (startVal.length === 16) startVal += ':00'; // Tambah detik 00 jika tidak ada
        // Tidak perlu substring jika sudah benar dari DB (biasanya YYYY-MM-DD HH:mm:ss -> T -> YYYY-MM-DDTHH:mm:ss)
        document.getElementById('edit_discount_start').value = startVal;
    } else {
        document.getElementById('edit_discount_start').value = '';
    }

    if (product.discount_end) {
        let endVal = product.discount_end.replace(' ', 'T');
        if (endVal.length === 16) endVal += ':00';
        document.getElementById('edit_discount_end').value = endVal;
    } else {
        document.getElementById('edit_discount_end').value = '';
    }
    
    // Trigger toggle logic initial state
    toggleDiscountStock('edit');
    
    document.getElementById('editForm').action = "/admin/products/" + product.id;
    
    openModal('editModal');
}

function toggleDiscountStock(mode) {
    const percentInput = document.getElementById(mode + '_discount_percentage');
    const stockInput = document.getElementById(mode + '_discount_stock');
    
    // Check if discount percentage has value > 0
    if (percentInput.value && parseFloat(percentInput.value) > 0) {
        stockInput.disabled = false;
        stockInput.style.background = 'white';
    } else {
        stockInput.disabled = true;
        stockInput.style.background = '#f3f4f6';
        stockInput.value = ''; // Clear value if disabled
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.style.display = 'none';
    }
}
</script>
@endsection
