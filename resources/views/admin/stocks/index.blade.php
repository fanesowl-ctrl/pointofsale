@extends('layouts.admin')

@section('title', 'Manajemen Stok (FIFO)')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="margin-bottom: 4px;">Sistem Stok FIFO</h1>
        <p style="color: #6b7280; font-size: 0.9rem;">Kelola barang masuk (Inbound) dan monitor umur stok.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button onclick="openInboundModal()" class="btn-primary" style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="ri-add-box-line"></i> Terima Barang (Inbound)
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

<!-- Filter Section -->
<div class="card" style="margin-bottom: 24px; display: flex; gap: 16px; padding: 16px; flex-wrap: wrap;">
    <a href="{{ route('admin.stocks.index') }}" 
       style="padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 500; font-size: 0.9rem; {{ !request('status') ? 'background: var(--primary-color); color: white;' : 'background: #f3f4f6; color: #4b5563;' }}">
       Semua Batch
    </a>
    <a href="{{ route('admin.stocks.index', ['status' => 'expired']) }}" 
       style="padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 500; font-size: 0.9rem; {{ request('status') == 'expired' ? 'background: #ef4444; color: white;' : 'background: #fee2e2; color: #991b1b;' }}">
       Kedaluwarsa (Expired)
    </a>
    <a href="{{ route('admin.stocks.index', ['status' => 'warning']) }}" 
       style="padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 500; font-size: 0.9rem; {{ request('status') == 'warning' ? 'background: #f59e0b; color: white;' : 'background: #fef3c7; color: #92400e;' }}">
       Hampir Expired (30 Hari)
    </a>
    
    <form action="{{ route('admin.stocks.index') }}" method="GET" style="margin-left: auto;">
        <input type="text" name="search" placeholder="Cari batch atau produk..." value="{{ request('search') }}" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; width: 250px;">
    </form>
</div>

<!-- Batch Table -->
<div class="card" style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
        <thead>
            <tr style="border-bottom: 2px solid #f3f4f6; text-align: left;">
                <th style="padding: 16px; font-weight: 600; color: #374151;">Produk</th>
                <th style="padding: 16px; font-weight: 600; color: #374151;">Kode Batch</th>
                <th style="padding: 16px; font-weight: 600; color: #374151;">Tgl Masuk (Received)</th>
                <th style="padding: 16px; font-weight: 600; color: #374151;">Kedaluwarsa (Expired)</th>
                <th style="padding: 16px; font-weight: 600; color: #374151;">Sisa Stok</th>
                <th style="padding: 16px; font-weight: 600; color: #374151;">Status</th>
                <th style="padding: 16px; font-weight: 600; color: #374151;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($batches as $batch)
            <tr style="border-bottom: 1px solid #f3f4f6; hover: background-color: #f9fafb;">
                <td style="padding: 16px;">
                    <div style="font-weight: 600; color: #1f2937;">{{ $batch->product->name }}</div>
                    <div style="font-size: 0.8rem; color: #6b7280;">{{ $batch->product->product_code }}</div>
                </td>
                <td style="padding: 16px; font-family: monospace; color: #4b5563;">
                    {{ $batch->batch_code ?? '-' }}
                </td>
                <td style="padding: 16px; color: #4b5563;">
                    {{ $batch->received_at ? \Carbon\Carbon::parse($batch->received_at)->format('d M Y') : '-' }}
                </td>
                <td style="padding: 16px;">
                    @if($batch->expiry_date)
                        {{ \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') }}
                        @php
                            $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($batch->expiry_date), false);
                        @endphp
                        @if($daysLeft < 0)
                            <span style="display: block; font-size: 0.75rem; color: #ef4444; font-weight: 600;">Sudah Expired!</span>
                        @elseif($daysLeft <= 30)
                            <span style="display: block; font-size: 0.75rem; color: #f59e0b; font-weight: 600;">{{ ceil($daysLeft) }} hari lagi</span>
                        @endif
                    @else
                        <span style="color: #9ca3af;">-</span>
                    @endif
                </td>
                <td style="padding: 16px;">
                    <span style="font-weight: 700; color: #1f2937;">{{ $batch->quantity }}</span>
                    <span style="font-size: 0.8rem; color: #9ca3af;">/ {{ $batch->original_quantity }}</span>
                </td>
                <td style="padding: 16px;">
                    <!-- Logic FIFO Status -->
                    @if($batch->quantity == 0)
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: #e5e7eb; color: #6b7280;">Habis</span>
                    @elseif($batch->expiry_date && \Carbon\Carbon::parse($batch->expiry_date)->isPast())
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: #fee2e2; color: #991b1b;">Expired</span>
                    @else
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: #d1fae5; color: #065f46;">Aktif</span>
                    @endif
                </td>
                <td style="padding: 16px;">
                    <button onclick='editBatch(@json($batch))' style="background: none; border: none; cursor: pointer; color: #d97706;" title="Edit Batch">
                        <i class="ri-pencil-line" style="font-size: 1.2rem;"></i>
                    </button>
                    <!-- Hapus batch jarang dilakukan kecuali salah input, krn audit trail -->
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">Belum ada data stok batch.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="padding: 16px;">
        {{ $batches->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal Inbound (Terima Barang) -->
<div id="inboundModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100; justify-content: center; align-items: center; overflow-y: auto;">
    <div class="modal-content" style="background: white; width: 600px; max-width: 95%; padding: 24px; border-radius: 16px; margin: 20px auto;">
        <h2 style="margin-bottom: 20px; font-size: 1.25rem;">Terima Barang Masuk (Inbound)</h2>
        <form action="{{ route('admin.stocks.store') }}" method="POST">
            @csrf
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Pilih Produk <span style="color: red">*</span></label>
                <!-- Searchable Select (Native Datalist for simplicity without extra JS libs) -->
                <input list="product_list" name="product_name_search" id="product_search_input" class="form-input" placeholder="Ketik nama atau kode barang..." 
                    style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" required autocomplete="off"
                    oninput="updateProductId(this)">
                <datalist id="product_list">
                    @foreach(\App\Models\Product::all() as $prod)
                        <option data-id="{{ $prod->id }}" value="{{ $prod->product_code }} - {{ $prod->name }}">
                    @endforeach
                </datalist>
                <input type="hidden" name="product_id" id="selected_product_id" required>
                <small style="color: #6b7280;">Pastikan memilih dari daftar yang muncul.</small>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Jumlah Masuk (Qty) <span style="color: red">*</span></label>
                    <input type="number" name="quantity" min="1" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" required>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Kode Batch (Opsional)</label>
                    <input type="text" name="batch_code" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" placeholder="Contoh: LOT-2023-A">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Tanggal Masuk <span style="color: red">*</span></label>
                    <input type="date" name="received_at" value="{{ date('Y-m-d') }}" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" required>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Tanggal Expired (Opsional)</label>
                    <input type="date" name="expiry_date" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
                     <small style="color: #6b7280;">Kosongkan jika tidak ada expired.</small>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                 <label style="display: block; margin-bottom: 6px; font-weight: 500;">Harga Beli per Unit (Opsional)</label>
                 <input type="number" name="cost_price" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" placeholder="Harga Modal saat ini">
                 <small style="color: #6b7280;">Jika kosong, akan menggunakan harga modal dari data master barang.</small>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeInboundModal()" style="padding: 10px 20px; background: #e5e7eb; border: none; border-radius: 8px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer;">Simpan Stok Masuk</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Batch -->
<div id="editBatchModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100; justify-content: center; align-items: center; overflow-y: auto;">
    <div class="modal-content" style="background: white; width: 500px; max-width: 95%; padding: 24px; border-radius: 16px; margin: 20px auto;">
        <h2 style="margin-bottom: 20px; font-size: 1.25rem;">Koreksi Batch Stok</h2>
        <form id="editBatchForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Produk (Read-only)</label>
                <input type="text" id="edit_product_name" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; background: #f3f4f6;" readonly>
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Sisa Stok (Koreksi Opname)</label>
                <input type="number" name="quantity" id="edit_quantity" min="0" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" required>
                <small style="color: #dc2626;">Perhatian: Mengubah sisa stokakan mempengaruhi Total Stok Barang master secara otomatis.</small>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                 <label style="display: block; margin-bottom: 6px; font-weight: 500;">Kode Batch</label>
                 <input type="text" name="batch_code" id="edit_batch_code" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                 <label style="display: block; margin-bottom: 6px; font-weight: 500;">Tanggal Expired</label>
                 <input type="date" name="expiry_date" id="edit_expiry_date" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeEditModal()" style="padding: 10px 20px; background: #e5e7eb; border: none; border-radius: 8px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openInboundModal() {
        document.getElementById('inboundModal').style.display = 'flex';
    }

    function closeInboundModal() {
        document.getElementById('inboundModal').style.display = 'none';
    }
    
    // Datalist Logic: Extract ID from selection
    function updateProductId(input) {
        const list = document.getElementById('product_list');
        const options = list.options;
        const hiddenInput = document.getElementById('selected_product_id');
        
        // Reset ID first
        hiddenInput.value = '';
        
        for(let i = 0; i < options.length; i++) {
            if(options[i].value === input.value) {
                hiddenInput.value = options[i].getAttribute('data-id');
                break;
            }
        }
    }

    function editBatch(batch) {
        document.getElementById('edit_product_name').value = batch.product.name;
        document.getElementById('edit_quantity').value = batch.quantity;
        document.getElementById('edit_batch_code').value = batch.batch_code || '';
        if (batch.expiry_date) {
            document.getElementById('edit_expiry_date').value = new Date(batch.expiry_date).toISOString().split('T')[0];
        } else {
            document.getElementById('edit_expiry_date').value = '';
        }

        document.getElementById('editBatchForm').action = "/admin/stocks/" + batch.id; // Pastikan route ini ada
        document.getElementById('editBatchModal').style.display = 'flex';
    }
    
    function closeEditModal() {
        document.getElementById('editBatchModal').style.display = 'none';
    }

    // Close Modals on Outside Click
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) {
             event.target.style.display = 'none';
        }
    }
</script>
@endsection
