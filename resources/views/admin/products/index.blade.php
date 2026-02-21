@extends('layouts.admin')

@section('title', 'Data Barang')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h1 class="page-title" style="margin-bottom: 0;">Data Barang</h1>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.products.export') }}" class="btn-secondary" style="text-decoration: none; padding: 10px 20px; background: #10b981; color: white; border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="ri-file-excel-2-line"></i> Export Excel
        </a>
        <button onclick="openModal('addModal')" class="btn-primary" style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="ri-add-line"></i> Tambah Barang
        </button>
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
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <form action="{{ route('admin.products.index') }}" method="GET" style="display: flex; align-items: center; gap: 10px;">
            <label>Show</label>
            <select name="per_page" onchange="this.form.submit()" style="padding: 6px; border-radius: 6px; border: 1px solid #d1d5db;">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            </select>
            <label>entries</label>
        </form>

        <form action="{{ route('admin.products.index') }}" method="GET" style="display: flex; align-items: center; gap: 10px;">
            <label style="font-weight: 500;">Kategori:</label>
            <select name="category" onchange="this.form.submit()" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #d1d5db; min-width: 150px;">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            @if(request('category'))
                <a href="{{ route('admin.products.index') }}" style="padding: 8px 12px; background: #f3f4f6; color: #6b7280; border-radius: 6px; text-decoration: none; font-size: 0.9rem;">Reset</a>
            @endif
        </form>

        <form action="{{ route('admin.products.index') }}" method="GET">
            <input type="text" name="search" placeholder="Cari nama barang..." value="{{ request('search') }}" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; width: 250px;">
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
                    @if($product->stock > 10)
                        <span style="padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: rgba(209, 250, 229, 0.9); color: #065f46; backdrop-filter: blur(4px);">
                            Stok: {{ $product->stock }}
                        </span>
                    @elseif($product->stock > 0)
                        <span style="padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: rgba(254, 243, 199, 0.9); color: #92400e; backdrop-filter: blur(4px);">
                            <i class="ri-alert-line"></i> Sisa: {{ $product->stock }}
                        </span>
                    @else
                        <span style="padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; background: rgba(254, 226, 226, 0.9); color: #991b1b; backdrop-filter: blur(4px);">
                            Habis
                        </span>
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
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #6b7280; font-size: 0.9rem;">Harga Jual</span>
                            <span style="font-weight: 700; color: #059669; font-size: 1.1rem;">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                        <span style="color: #6b7280; font-size: 0.85rem;">Harga Modal</span>
                        <span style="color: #9ca3af; font-size: 0.85rem;">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <button type="button" onclick='editProduct(@json($product))' 
                        style="display: flex; align-items: center; justify-content: center; gap: 6px; padding: 10px; background: #fef3c7; color: #d97706; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; transition: background 0.2s;">
                        <i class="ri-pencil-line"></i> Edit
                    </button>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?');" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 6px; padding: 10px; background: #fee2e2; color: #dc2626; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; transition: background 0.2s;">
                            <i class="ri-delete-bin-line"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 48px; background: white; border-radius: 12px; color: #6b7280;">
            <i class="ri-inbox-line" style="font-size: 3rem; margin-bottom: 16px; display: block;"></i>
            <p>Tidak ada data barang ditemukan.</p>
        </div>
        @endforelse
    </div>

    <div class="card" style="padding: 16px; margin-bottom: 24px;">
        {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
    </div>

<!-- Modal Tambah Barang -->
<div id="addModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100; justify-content: center; align-items: center; overflow-y: auto;">
    <div class="modal-content" style="background: white; width: 500px; max-width: 90%; padding: 24px; border-radius: 16px; animation: slideUp 0.3s ease; max-height: 90vh; overflow-y: auto; margin: 20px auto;">
        <h2 style="margin-bottom: 20px; font-size: 1.5rem;">Tambah Barang</h2>
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Kode Barang</label>
                <input type="text" name="product_code" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                    oninvalid="this.setCustomValidity('Harap isi kode barang.')"
                    oninput="this.setCustomValidity('')">
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Nama Barang</label>
                <input type="text" name="name" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                    oninvalid="this.setCustomValidity('Harap isi nama barang.')"
                    oninput="this.setCustomValidity('')">
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Kategori</label>
                <input type="text" name="category" class="form-input" placeholder="Contoh: Makanan, Minuman, Elektronik" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Harga Awal (Rp)</label>
                    <input type="number" name="cost_price" required min="0" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                        oninvalid="this.setCustomValidity('Harap isi harga awal.')"
                        oninput="this.setCustomValidity('')">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Harga Jual (Rp)</label>
                    <input type="number" name="selling_price" required min="0" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                        oninvalid="this.setCustomValidity('Harap isi harga jual.')"
                        oninput="this.setCustomValidity('')">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Diskon (%) <span style="color: #9ca3af; font-weight: 400; font-size: 0.9rem;">(Opsional)</span></label>
                    <input type="number" name="discount_percentage" id="add_discount_percentage" min="0" max="100" step="0.01" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" placeholder="0-100" oninput="toggleDiscountStock('add')">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Stok Promo <span style="color: #9ca3af; font-weight: 400; font-size: 0.9rem;">(Opsional)</span></label>
                    <input type="number" name="discount_stock" id="add_discount_stock" min="0" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" placeholder="Jumlah Promo" disabled style="background: #f3f4f6;">
                    <small style="color: #6b7280; font-size: 0.75rem;">Kosongkan jika unlimited</small>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Stok Barang</label>
                <input type="number" name="stock" required min="0" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                    oninvalid="this.setCustomValidity('Harap isi jumlah stok.')"
                    oninput="this.setCustomValidity('')">
            </div>
            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Gambar Barang (Opsional)</label>
                <input type="file" name="image" accept="image/*" class="form-input" 
                    style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                    onchange="previewImage(event, 'add_preview')">
                <div id="add_preview" style="margin-top: 10px; display: none;">
                    <img id="add_preview_img" style="max-width: 100%; max-height: 150px; border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <small style="color: #6b7280; display: block; margin-top: 5px;">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('addModal')" style="padding: 10px 20px; background: #e5e7eb; border: none; border-radius: 8px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer;">Simpan Barang</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Barang -->
<div id="editModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100; justify-content: center; align-items: center; overflow-y: auto;">
    <div class="modal-content" style="background: white; width: 500px; max-width: 90%; padding: 24px; border-radius: 16px; max-height: 90vh; overflow-y: auto; margin: 20px auto;">
        <h2 style="margin-bottom: 20px; font-size: 1.5rem;">Edit Barang</h2>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Kode Barang</label>
                <input type="text" id="edit_product_code" name="product_code" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                    oninvalid="this.setCustomValidity('Harap isi kode barang.')"
                    oninput="this.setCustomValidity('')">
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Nama Barang</label>
                <input type="text" id="edit_name" name="name" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                    oninvalid="this.setCustomValidity('Harap isi nama barang.')"
                    oninput="this.setCustomValidity('')">
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Kategori</label>
                <input type="text" id="edit_category" name="category" class="form-input" placeholder="Contoh: Makanan, Minuman, Elektronik" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Harga Awal (Rp)</label>
                    <input type="number" id="edit_cost_price" name="cost_price" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                        oninvalid="this.setCustomValidity('Harap isi harga awal.')"
                        oninput="this.setCustomValidity('')">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Harga Jual (Rp)</label>
                    <input type="number" id="edit_selling_price" name="selling_price" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                        oninvalid="this.setCustomValidity('Harap isi harga jual.')"
                        oninput="this.setCustomValidity('')">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Diskon (%) <span style="color: #9ca3af; font-weight: 400; font-size: 0.9rem;">(Opsional)</span></label>
                    <input type="number" id="edit_discount_percentage" name="discount_percentage" min="0" max="100" step="0.01" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" placeholder="0-100" oninput="toggleDiscountStock('edit')">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Stok Promo <span style="color: #9ca3af; font-weight: 400; font-size: 0.9rem;">(Opsional)</span></label>
                    <input type="number" id="edit_discount_stock" name="discount_stock" min="0" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" placeholder="Jumlah Promo">
                    <small style="color: #6b7280; font-size: 0.75rem;">Kosongkan jika unlimited</small>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Stok Barang</label>
                <input type="number" id="edit_stock" name="stock" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                    oninvalid="this.setCustomValidity('Harap isi jumlah stok.')"
                    oninput="this.setCustomValidity('')">
            </div>
            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Gambar Barang (Opsional)</label>
                <div id="edit_current_image" style="margin-bottom: 10px; display: none;">
                    <p style="font-size: 0.85rem; color: #6b7280; margin-bottom: 5px;">Gambar saat ini:</p>
                    <img id="edit_current_img" style="max-width: 100%; max-height: 150px; border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <input type="file" name="image" accept="image/*" class="form-input" 
                    style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                    onchange="previewImage(event, 'edit_preview')">
                <div id="edit_preview" style="margin-top: 10px; display: none;">
                    <p style="font-size: 0.85rem; color: #6b7280; margin-bottom: 5px;">Preview gambar baru:</p>
                    <img id="edit_preview_img" style="max-width: 100%; max-height: 150px; border-radius: 8px; border: 1px solid #e5e7eb;">
                </div>
                <small style="color: #6b7280; display: block; margin-top: 5px;">Kosongkan jika tidak ingin mengubah gambar. Format: JPG, PNG, GIF. Maksimal 2MB.</small>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('editModal')" style="padding: 10px 20px; background: #e5e7eb; border: none; border-radius: 8px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer;">Update Barang</button>
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
    
    // Reset preview images when closing
    if (modalId === 'addModal') {
        document.getElementById('add_preview').style.display = 'none';
    } else if (modalId === 'editModal') {
        document.getElementById('edit_preview').style.display = 'none';
        document.getElementById('edit_current_image').style.display = 'none';
    }
}

function previewImage(event, previewId) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewDiv = document.getElementById(previewId);
            const previewImg = document.getElementById(previewId + '_img');
            previewImg.src = e.target.result;
            previewDiv.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}

function editProduct(product) {
    document.getElementById('edit_product_code').value = product.product_code;
    document.getElementById('edit_name').value = product.name;
    document.getElementById('edit_category').value = product.category || '';
    document.getElementById('edit_cost_price').value = Math.floor(product.cost_price); // Remove decimals for input
    document.getElementById('edit_selling_price').value = Math.floor(product.selling_price);
    document.getElementById('edit_discount_percentage').value = product.discount_percentage || '';
    document.getElementById('edit_discount_stock').value = product.discount_stock || '';
    
    // Trigger toggle logic initial state
    toggleDiscountStock('edit');
    
    document.getElementById('edit_stock').value = product.stock;
    
    // Show current image if exists
    const currentImageDiv = document.getElementById('edit_current_image');
    const currentImg = document.getElementById('edit_current_img');
    if (product.image) {
        currentImg.src = '/storage/' + product.image;
        currentImageDiv.style.display = 'block';
    } else {
        currentImageDiv.style.display = 'none';
    }
    
    // Hide preview
    document.getElementById('edit_preview').style.display = 'none';
    
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
