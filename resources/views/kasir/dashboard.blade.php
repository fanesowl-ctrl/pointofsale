@extends('layouts.kasir')

@section('title', 'Dashboard')

@section('content')
    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="welcome-card" style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h1 style="font-size: 1.5rem; color: #1f2937; margin-bottom: 10px;">Halo, {{ session('kasir_name') }}!</h1>
        <p style="color: #6b7280;">Selamat datang kembali di panel kasir. Siap melayani pelanggan hari ini?</p>
    </div>

    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <!-- Card Transaksi -->
        <div class="card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center;">
            <div style="width: 50px; height: 50px; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                <i class="ri-shopping-cart-2-line" style="font-size: 1.5rem; color: #0284c7;"></i>
            </div>
            <h3 style="color: #374151; margin-bottom: 5px;">Transaksi Baru</h3>
            <p style="color: #9ca3af; font-size: 0.9rem; margin-bottom: 20px;">Input penjualan baru</p>
            <a href="{{ route('kasir.transaksi.create') }}" style="display: inline-block; padding: 8px 16px; background: #0ea5e9; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9rem;">Mulai Transaksi</a>
        </div>

        <!-- Card Laporan -->
        <div class="card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center;">
            <div style="width: 50px; height: 50px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                <i class="ri-file-list-3-line" style="font-size: 1.5rem; color: #d97706;"></i>
            </div>
            <h3 style="color: #374151; margin-bottom: 5px;">Laporan Harian</h3>
            <p style="color: #9ca3af; font-size: 0.9rem; margin-bottom: 20px;">Cek omset hari ini</p>
            <a href="#" style="display: inline-block; padding: 8px 16px; background: #f59e0b; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9rem;">Lihat Laporan</a>
        </div>
    </div>
@endsection

