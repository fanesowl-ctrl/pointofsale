# Fitur Pengelolaan Barang Diskon

## Deskripsi
Fitur ini memungkinkan admin untuk mengelola diskon produk dalam aplikasi Point of Sale. Admin dapat menambahkan persentase diskon pada setiap produk, dan sistem akan otomatis menghitung harga setelah diskon.

## Fitur yang Ditambahkan

### 1. Database Schema
Menambahkan 4 kolom baru pada tabel `products`:
- `discount_percentage` (DECIMAL 5,2): Persentase diskon (0-100%)
- `discount_amount` (DECIMAL 15,2): Nominal diskon dalam rupiah (dihitung otomatis)
- `final_price` (DECIMAL 15,2): Harga setelah diskon (dihitung otomatis)
- `discount_stock` (INT, Nullable): Batas kuota jumlah barang yang didiskon (Baru!)

### 2. Perhitungan Otomatis
Sistem akan otomatis menghitung:
- **Discount Amount** = (Selling Price × Discount Percentage) ÷ 100
- **Final Price** = Selling Price - Discount Amount

### 3. Fitur Kuota Diskon (Stok Promo)
Admin bisa membatasi berapa jumlah barang yang mendapatkan diskon.
- **Unlimited**: Kosongkan field "Stok Promo". Diskon berlaku untuk semua stok.
- **Limited**: Isi angka (misal 10). Hanya 10 pembeli pertama yang dapat diskon.
- **Auto-Nonaktif**: Saat kuota promo habis (`discount_stock` mencapai 0), diskon produk otomatis dinonaktifkan (kembali ke harga normal).

### 4. Tampilan Admin
#### Product Card
- Badge diskon merah di pojok kiri atas gambar produk (jika ada diskon)
- Menampilkan harga normal (dicoret) dan harga diskon
- Menampilkan total penghematan dalam box merah muda

#### Form Input
- Field "Diskon (%)" pada modal Tambah Barang
- Field **"Stok Promo"** (disabled jika diskon 0)
- Validasi: 0-100%, opsional
- Support desimal (contoh: 12.5%)

### 5. Tampilan Kasir
- Menampilkan Badge **"Sisa Promo: X"** jika kuota terbatas
- Harga otomatis berubah menjadi normal jika kuota habis
- Transaksi otomatis mengurangi kuota promo

### 4. Export Data
File CSV export akan mencakup kolom tambahan:
- Diskon (%)
- Harga Setelah Diskon

### 5. API Response
API endpoint `/api/products` akan mengembalikan field tambahan:
- `discount_percentage`
- `discount_amount`
- `final_price`

## Cara Menggunakan

### Menambah Produk dengan Diskon
1. Klik tombol "Tambah Barang"
2. Isi data produk seperti biasa
3. Pada field "Diskon (%)", masukkan persentase diskon (contoh: 10 untuk diskon 10%)
4. Kosongkan field diskon jika tidak ada diskon
5. Klik "Simpan Barang"

### Mengubah Diskon Produk
1. Klik tombol "Edit" pada product card
2. Ubah nilai pada field "Diskon (%)"
3. Klik "Update Barang"

### Menghapus Diskon
1. Klik tombol "Edit" pada product card
2. Kosongkan field "Diskon (%)" atau isi dengan 0
3. Klik "Update Barang"

## Instalasi

### 1. Jalankan Migration
```bash
php artisan migrate
```

Migration akan menambahkan kolom diskon ke tabel products yang sudah ada.

### 2. Update Data Existing (Opsional)
Jika Anda memiliki data produk yang sudah ada, kolom diskon akan otomatis terisi dengan nilai default 0.

## Validasi
- Diskon harus berupa angka
- Diskon minimal 0%
- Diskon maksimal 100%
- Field diskon bersifat opsional (boleh kosong)

## Catatan Penting
1. **Backward Compatible**: Produk lama tanpa diskon akan tetap berfungsi normal
2. **Perhitungan Otomatis**: Admin hanya perlu input persentase, sistem akan hitung nominal dan harga final
3. **Visual Menarik**: Badge diskon merah dengan gradient dan shadow untuk menarik perhatian
4. **Responsive**: Tampilan diskon menyesuaikan dengan ada/tidaknya diskon pada produk

## Troubleshooting

### Migration Error: "could not find driver"
Ini berarti PHP extension untuk MySQL belum aktif. Solusi:
1. Buka file `php.ini`
2. Cari baris `;extension=pdo_mysql`
3. Hapus tanda `;` di depannya menjadi `extension=pdo_mysql`
4. Restart Apache/Nginx dan PHP-FPM
5. Jalankan kembali `php artisan migrate`

### Diskon Tidak Muncul di Produk Lama
Produk yang dibuat sebelum fitur ini akan memiliki diskon 0%. Anda perlu edit produk tersebut untuk menambahkan diskon.

## File yang Dimodifikasi
1. `database/migrations/2026_02_07_000001_add_discount_to_products_table.php` (NEW)
2. `app/Models/Product.php`
3. `app/Http/Controllers/Admin/ProductController.php`
4. `resources/views/admin/products/index.blade.php`

## API Example Response
```json
{
  "success": true,
  "message": "List Product",
  "data": {
    "data": [
      {
        "id": 1,
        "product_code": "BRG001",
        "name": "Produk Contoh",
        "cost_price": 50000,
        "selling_price": 100000,
        "discount_percentage": 10,
        "discount_amount": 10000,
        "final_price": 90000,
        "stock": 50,
        "image": "products/example.jpg",
        "created_at": "2026-02-07T01:00:00.000000Z",
        "updated_at": "2026-02-07T01:00:00.000000Z"
      }
    ]
  }
}
```
