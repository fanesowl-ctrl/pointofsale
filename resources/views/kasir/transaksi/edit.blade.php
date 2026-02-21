@extends('layouts.kasir')

@section('title', 'Edit Transaksi')

@section('content')
<div style="display: flex; gap: 24px;">
    <!-- Form Input (Kiri) -->
    <div style="flex: 1;">
        <div class="card">
            <h2 style="font-size: 1.25rem; margin-bottom: 20px;">Edit Transaksi: {{ $transaksi->nomor_transaksi }}</h2>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Nama Barang</label>
                <select id="product_select" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" onchange="updateProductInfo()">
                    <option value="" selected disabled>-- Pilih Barang --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->stock }}" data-name="{{ $product->name }}">
                            {{ $product->product_code }} - {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Stok Tersedia</label>
                    <input type="text" id="stock_display" readonly class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; background: #f3f4f6;">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500;">Harga Barang</label>
                    <input type="text" id="price_display" readonly class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; background: #f3f4f6;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Jumlah Beli (Total Stok)</label>
                <input type="number" id="qty_input" min="1" value="1" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" oninput="calculateSubtotal()">
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Total Harga Item</label>
                <input type="text" id="subtotal_display" readonly class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; background: #f3f4f6; font-weight: bold;">
            </div>

            <button type="button" onclick="addToCart()" style="width: 100%; padding: 12px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                <i class="ri-add-circle-line"></i> Tambah ke Keranjang
            </button>
        </div>
        
        <a href="{{ route('kasir.transaksi.index') }}" style="display: block; text-align: center; margin-top: 20px; color: #6b7280; text-decoration: none;">&larr; Kembali ke Riwayat</a>
    </div>

    <!-- Tabel Keranjang (Kanan) -->
    <div style="flex: 2;">
        <div class="card" style="min-height: 500px; display: flex; flex-direction: column;">
            <h2 style="font-size: 1.25rem; margin-bottom: 20px; display: flex; justify-content: space-between;">
                Keranjang Belanja
                <span style="font-size: 0.9rem; color: #6b7280;" id="current_date">{{ date('d M Y') }}</span>
            </h2>

            <div style="flex: 1;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb; text-align: left;">
                            <th style="padding: 10px; border-bottom: 2px solid #e5e7eb;">Barang</th>
                            <th style="padding: 10px; border-bottom: 2px solid #e5e7eb;">Harga</th>
                            <th style="padding: 10px; border-bottom: 2px solid #e5e7eb;">Qty</th>
                            <th style="padding: 10px; border-bottom: 2px solid #e5e7eb;">Total</th>
                            <th style="padding: 10px; border-bottom: 2px solid #e5e7eb;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cart_table_body">
                        <!-- Items will be added here -->
                    </tbody>
                </table>
                <div id="empty_cart_msg" style="text-align: center; padding: 40px; color: #9ca3af;">
                    <i class="ri-shopping-basket-line" style="font-size: 3rem; margin-bottom: 10px; display: block;"></i>
                    Belum ada barang di keranjang.
                </div>
            </div>

            <div style="border-top: 2px solid #e5e7eb; padding-top: 20px; margin-top: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <span style="font-size: 1.25rem; font-weight: 600;">Grand Total</span>
                    <span style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);" id="grand_total_display">Rp 0</span>
                </div>
                
                <button onclick="updateTransaction()" id="btn_process" disabled style="width: 100%; padding: 14px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 1.1rem; font-weight: 600; opacity: 0.5;">
                    Update Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];

    // Load existing transaction items
    @foreach($transaksi->details as $detail)
        cart.push({
            product_id: "{{ $detail->product_id }}",
            name: "{{ $detail->product->name }}",
            price: {{ $detail->price }},
            quantity: {{ $detail->quantity }},
            total: {{ $detail->total }}
        });
    @endforeach

    // Render cart on page load
    renderCart();

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number);
    }

    function updateProductInfo() {
        const select = document.getElementById('product_select');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.value) {
            const price = parseFloat(selectedOption.dataset.price);
            const stock = parseInt(selectedOption.dataset.stock);
            
            document.getElementById('price_display').value = formatRupiah(price);
            document.getElementById('stock_display').value = stock;
            document.getElementById('qty_input').max = stock;
            document.getElementById('qty_input').value = 1;
            
            calculateSubtotal();
        }
    }

    function calculateSubtotal() {
        const select = document.getElementById('product_select');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption?.value) {
            const price = parseFloat(selectedOption.dataset.price);
            const qty = parseInt(document.getElementById('qty_input').value) || 0;
            const subtotal = price * qty;
            
            document.getElementById('subtotal_display').value = formatRupiah(subtotal);
        }
    }

    function addToCart() {
        const select = document.getElementById('product_select');
        const selectedOption = select.options[select.selectedIndex];
        
        if (!selectedOption.value) return alert('Pilih barang terlebih dahulu!');
        
        const productId = selectedOption.value;
        const name = selectedOption.dataset.name;
        const price = parseFloat(selectedOption.dataset.price);
        const stock = parseInt(selectedOption.dataset.stock);
        const qty = parseInt(document.getElementById('qty_input').value);

        if (qty <= 0) return alert('Jumlah harus lebih dari 0');
        if (qty > stock) return alert('Stok tidak mencukupi!');

        // Check if exists
        const existingItem = cart.find(item => item.product_id === productId);
        if (existingItem) {
            if (existingItem.quantity + qty > stock) {
                return alert('Total stok di keranjang melebihi stok tersedia!');
            }
            existingItem.quantity += qty;
            existingItem.total = existingItem.quantity * price;
        } else {
            cart.push({
                product_id: productId,
                name: name,
                price: price,
                quantity: qty,
                total: qty * price
            });
        }

        renderCart();
        resetForm();
    }

    function resetForm() {
        document.getElementById('product_select').value = "";
        document.getElementById('stock_display').value = "";
        document.getElementById('price_display').value = "";
        document.getElementById('qty_input').value = 1;
        document.getElementById('subtotal_display').value = "";
    }

    function renderCart() {
        const tbody = document.getElementById('cart_table_body');
        tbody.innerHTML = '';
        
        let grandTotal = 0;

        cart.forEach((item, index) => {
            grandTotal += item.total;
            
            tbody.innerHTML += `
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 10px;">${item.name}</td>
                    <td style="padding: 10px;">${formatRupiah(item.price)}</td>
                    <td style="padding: 10px;">${item.quantity}</td>
                    <td style="padding: 10px;">${formatRupiah(item.total)}</td>
                    <td style="padding: 10px;">
                        <button onclick="removeFromCart(${index})" style="color: #ef4444; background: none; border: none; cursor: pointer; font-size: 1.1rem;">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        document.getElementById('grand_total_display').innerText = formatRupiah(grandTotal);
        
        const emptyMsg = document.getElementById('empty_cart_msg');
        const btnProcess = document.getElementById('btn_process');
        
        if (cart.length > 0) {
            emptyMsg.style.display = 'none';
            btnProcess.disabled = false;
            btnProcess.style.opacity = 1;
            btnProcess.style.background = '#10b981';
        } else {
            emptyMsg.style.display = 'block';
            btnProcess.disabled = true;
            btnProcess.style.opacity = 0.5;
        }
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    async function updateTransaction() {
        if (!confirm('Yakin ingin mengupdate transaksi ini?')) return;

        try {
            const response = await fetch("{{ route('kasir.transaksi.update', $transaksi->id) }}", {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ items: cart })
            });

            const result = await response.json();

            if (result.success) {
                alert('Transaksi Berhasil Diupdate!');
                window.location.href = result.redirect;
            } else {
                alert('Gagal: ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan sistem.');
        }
    }
</script>
@endsection
