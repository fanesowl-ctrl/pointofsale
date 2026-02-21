# Fitur Upload Gambar Barang - Dokumentasi

## Perubahan yang Dilakukan

### 1. Database
- **Migration baru**: `2024_01_10_000003_add_image_to_products_table.php`
- Menambahkan kolom `image` (VARCHAR 255, nullable) ke tabel `products`

### 2. Model
- **File**: `app/Models/Product.php`
- Menambahkan `'image'` ke array `$fillable`

### 3. Controller
- **File**: `app/Http/Controllers/Admin/ProductController.php`
- **Method `store()`**: 
  - Validasi upload gambar (format: jpeg, png, jpg, gif | max: 2MB)
  - Menyimpan gambar ke folder `storage/app/public/products`
  - Menyimpan path gambar ke database
- **Method `update()`**: 
  - Validasi upload gambar baru
  - Menghapus gambar lama jika ada
  - Menyimpan gambar baru

### 4. View
- **File**: `resources/views/admin/products/index.blade.php`
- **Tampilan Grid Card**:
  - Menampilkan gambar produk jika ada
  - Fallback ke icon jika tidak ada gambar
  - Tinggi gambar: 200px dengan object-fit: cover
- **Form Tambah Barang**:
  - Input file untuk upload gambar
  - Preview gambar sebelum submit
  - Validasi format dan ukuran
- **Form Edit Barang**:
  - Menampilkan gambar saat ini
  - Input file untuk upload gambar baru
  - Preview gambar baru sebelum submit
  - Opsi untuk tidak mengubah gambar

### 5. JavaScript
- **Function `previewImage()`**: Preview gambar sebelum upload
- **Function `editProduct()`**: Menampilkan gambar saat ini saat edit
- **Function `closeModal()`**: Reset preview saat modal ditutup

## Cara Menggunakan

### Menambah Barang dengan Gambar
1. Klik tombol "Tambah Barang"
2. Isi semua field yang diperlukan
3. Klik "Pilih File" pada field "Gambar Barang"
4. Pilih gambar (JPG, PNG, GIF, max 2MB)
5. Preview gambar akan muncul
6. Klik "Simpan Barang"

### Mengedit Gambar Barang
1. Klik tombol "Edit" pada card produk
2. Gambar saat ini akan ditampilkan (jika ada)
3. Untuk mengubah gambar, pilih file baru
4. Preview gambar baru akan muncul
5. Untuk tidak mengubah gambar, biarkan kosong
6. Klik "Update Barang"

## Instalasi

### Jika Migration Berhasil
```bash
php artisan migrate
```

### Jika Migration Gagal (Driver MySQL Issue)
Jalankan SQL berikut secara manual di phpMyAdmin atau MySQL client:
```sql
ALTER TABLE `products` ADD COLUMN `image` VARCHAR(255) NULL AFTER `stock`;
```

File SQL tersedia di: `database/add_image_column.sql`

### Storage Link
Storage link sudah dibuat. Jika perlu membuat ulang:
```bash
php artisan storage:link
```

## Lokasi Penyimpanan Gambar
- **Path Fisik**: `storage/app/public/products/`
- **Path URL**: `public/storage/products/`
- **Format Nama File**: `timestamp_namafile.ext`
  Contoh: `1706252400_laptop.jpg`

## Validasi
- **Format**: JPEG, PNG, JPG, GIF
- **Ukuran Maksimal**: 2MB (2048 KB)
- **Field**: Opsional (nullable)

## Catatan Penting
1. Gambar lama akan otomatis dihapus saat update dengan gambar baru
2. Jika tidak memilih gambar baru saat edit, gambar lama tetap dipertahankan
3. Gambar ditampilkan dengan `object-fit: cover` untuk menjaga proporsi
4. Fallback icon (shopping bag) ditampilkan jika produk tidak memiliki gambar
