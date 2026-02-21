# Panduan Menjalankan SQL Script untuk Fitur Diskon

## 📋 File SQL yang Tersedia

1. **add_discount_columns.sql** - Menambahkan kolom diskon utama (WAJIB PERTAMA)
2. **add_discount_quota.sql** - Menambahkan kolom kuota/stok promo (WAJIB KEDUA - FITUR BARU)
3. **sample_discount_products.sql** - Contoh data produk dengan diskon (OPSIONAL)

---

## 🚀 Cara Menjalankan SQL Script

### Metode 1: Menggunakan phpMyAdmin (Paling Mudah)

#### Langkah 1: Buka phpMyAdmin
1. Buka browser, akses: `http://localhost/phpmyadmin`
2. Login jika diminta (biasanya username: `root`, password: kosong)

#### Langkah 2: Pilih Database
1. Klik database `pos` di sidebar kiri

#### Langkah 3: Jalankan SQL untuk Menambah Kolom
1. Klik tab **SQL** di bagian atas
2. Buka file `database/add_discount_columns.sql` dengan text editor
3. Copy semua isi file tersebut
4. Paste ke kolom SQL di phpMyAdmin
5. Klik tombol **Go** atau **Kirim**
6. Jika berhasil, akan muncul pesan sukses

#### Langkah 4: Jalankan SQL untuk Contoh Data (Opsional)
1. Masih di tab **SQL**
2. Buka file `database/sample_discount_products.sql` dengan text editor
3. Copy semua isi file tersebut
4. Paste ke kolom SQL di phpMyAdmin
5. Klik tombol **Go** atau **Kirim**
6. Akan muncul tabel hasil dengan 5 produk contoh

---

### Metode 2: Menggunakan Command Line (Terminal)

#### Langkah 1: Buka Terminal
1. Buka Command Prompt atau PowerShell
2. Masuk ke folder Laragon MySQL bin:
```bash
cd C:\laragon\bin\mysql\mysql-8.0.30\bin
```
(Sesuaikan versi MySQL Anda)

#### Langkah 2: Login ke MySQL
```bash
mysql -u root -p
```
(Tekan Enter jika tidak ada password)

#### Langkah 3: Pilih Database
```sql
USE pos;
```

#### Langkah 4: Jalankan SQL File
```bash
source C:/laragon/www/pointofsale/database/add_discount_columns.sql
```

Untuk contoh data:
```bash
source C:/laragon/www/pointofsale/database/sample_discount_products.sql
```

---

### Metode 3: Menggunakan HeidiSQL (Jika Tersedia)

1. Buka HeidiSQL
2. Connect ke database `pos`
3. Klik menu **File** → **Load SQL file...**
4. Pilih file `add_discount_columns.sql`
5. Klik tombol **Execute** (F9)
6. Ulangi untuk `sample_discount_products.sql` jika ingin contoh data

---

## ✅ Verifikasi Hasil

Setelah menjalankan SQL, verifikasi dengan query berikut di phpMyAdmin:

```sql
-- Cek struktur tabel
DESCRIBE products;
```

Anda harus melihat kolom baru:
- `discount_percentage`
- `discount_amount`
- `final_price`

```sql
-- Cek data produk (jika sudah insert contoh)
SELECT 
    product_code,
    name,
    selling_price,
    discount_percentage,
    discount_amount,
    final_price,
    stock
FROM products
ORDER BY id DESC
LIMIT 10;
```

---

## 🎯 Urutan Eksekusi yang Benar

1. ✅ **PERTAMA**: Jalankan `add_discount_columns.sql`
2. ✅ **KEDUA** (Opsional): Jalankan `sample_discount_products.sql`

**JANGAN** dibalik urutannya!

---

## ⚠️ Troubleshooting

### Error: "Duplicate column name"
**Penyebab**: Kolom sudah ada sebelumnya
**Solusi**: Abaikan error ini, artinya kolom sudah ditambahkan

### Error: "Table 'pos.products' doesn't exist"
**Penyebab**: Database atau tabel belum dibuat
**Solusi**: 
1. Pastikan database `pos` sudah ada
2. Jalankan migration Laravel terlebih dahulu: `php artisan migrate`

### Error: "Duplicate entry for key 'product_code'"
**Penyebab**: Kode produk contoh sudah ada
**Solusi**: 
1. Hapus produk dengan kode DISC001-DISC004 dan NODISC001 terlebih dahulu, atau
2. Edit file SQL, ubah kode produknya

---

## 📊 Hasil yang Diharapkan

Setelah berhasil, Anda akan memiliki:

✅ Tabel `products` dengan 3 kolom baru untuk diskon
✅ Semua produk lama memiliki nilai default (diskon 0%)
✅ 5 produk contoh dengan berbagai tingkat diskon (jika menjalankan sample)

---

## 🔄 Rollback (Jika Ingin Menghapus Fitur)

Jika ingin menghapus kolom diskon:

```sql
ALTER TABLE `products` 
DROP COLUMN `discount_percentage`,
DROP COLUMN `discount_amount`,
DROP COLUMN `final_price`;
```

**PERINGATAN**: Ini akan menghapus semua data diskon yang sudah ada!

---

## 📞 Bantuan Lebih Lanjut

Jika mengalami kesulitan, silakan hubungi developer atau cek dokumentasi di `FITUR_DISKON.md`
