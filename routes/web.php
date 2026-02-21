<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (Protected)
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Data Barang Routes
        Route::get('/products/discounted', [\App\Http\Controllers\Admin\ProductController::class, 'discountedIndex'])->name('products.discounted');
        
        // Manajemen Stok (FIFO)
        Route::resource('stocks', \App\Http\Controllers\Admin\StockController::class);

        // Manajemen Member
        Route::resource('members', \App\Http\Controllers\Admin\MemberController::class);
        
        Route::get('/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [\App\Http\Controllers\Admin\ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/products/export', [\App\Http\Controllers\Admin\ProductController::class, 'export'])->name('products.export');

        // Laporan Bulanan Routes
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{year}/{month}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');
        Route::get('/reports/{year}/{month}/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('reports.pdf');
        // Payment Settings (QRIS)
        Route::get('/payment-settings', [\App\Http\Controllers\Admin\PaymentSettingsController::class, 'index'])->name('payment-settings.index');
        Route::post('/payment-settings', [\App\Http\Controllers\Admin\PaymentSettingsController::class, 'update'])->name('payment-settings.update');
        Route::delete('/payment-settings', [\App\Http\Controllers\Admin\PaymentSettingsController::class, 'destroy'])->name('payment-settings.destroy');

        // Kasir Routes (Data Table Kasirs)
        Route::get('/kasirs', [\App\Http\Controllers\Admin\KasirController::class, 'index'])->name('kasirs.index');
        Route::post('/kasirs', [\App\Http\Controllers\Admin\KasirController::class, 'store'])->name('kasirs.store');
        Route::put('/kasirs/{id}', [\App\Http\Controllers\Admin\KasirController::class, 'update'])->name('kasirs.update');
        Route::delete('/kasirs/{id}', [\App\Http\Controllers\Admin\KasirController::class, 'destroy'])->name('kasirs.destroy');
    });
});

// Route Khusus Kasir
Route::prefix('kasir')->name('kasir.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        // Cek Manual Session Kasir
        if (!session('is_kasir_logged_in')) {
            return redirect('/admin/login')->withErrors(['email' => 'Silakan login sebagai kasir.']);
        }
        return view('kasir.dashboard');
    })->name('dashboard');

    // Transaksi Routes
    Route::get('/transaksi', [\App\Http\Controllers\Kasir\TransactionController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/baru', [\App\Http\Controllers\Kasir\TransactionController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi', [\App\Http\Controllers\Kasir\TransactionController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{id}/edit', [\App\Http\Controllers\Kasir\TransactionController::class, 'edit'])->name('transaksi.edit');
    Route::put('/transaksi/{id}', [\App\Http\Controllers\Kasir\TransactionController::class, 'update'])->name('transaksi.update');
    Route::delete('/transaksi/{id}', [\App\Http\Controllers\Kasir\TransactionController::class, 'destroy'])->name('transaksi.destroy');

    // Validasi Member
    Route::post('/check-member', [\App\Http\Controllers\Kasir\TransactionController::class, 'checkMember'])->name('check-member');

    // Laporan Routes
    Route::get('/laporan', [\App\Http\Controllers\Kasir\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/{tanggal}', [\App\Http\Controllers\Kasir\LaporanController::class, 'detail'])->name('laporan.detail');
    Route::get('/laporan/{tanggal}/pdf', [\App\Http\Controllers\Kasir\LaporanController::class, 'exportPdf'])->name('laporan.pdf');

    // Logout Kasir menggunakan method logout yang sama dengan admin agar bersih total
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

});
