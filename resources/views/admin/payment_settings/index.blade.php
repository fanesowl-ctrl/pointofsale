@extends('layouts.admin')

@section('title', 'Pengaturan Pembayaran')

@section('content')
<h1 class="page-title">Pengaturan Pembayaran</h1>

<div class="card" style="max-width: 600px;">
    <h2 style="font-size: 1.25rem; margin-bottom: 20px;">QRIS Settings</h2>

    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-group" style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Gambar QRIS Saat Ini</label>
        @if($qrisImage)
            <div style="margin-bottom: 15px;">
                <div style="display: inline-block; padding: 10px; border: 1px dashed #d1d5db; border-radius: 8px; margin-bottom: 10px;">
                    <img src="{{ asset('storage/' . $qrisImage) }}" alt="QRIS Code" style="max-width: 200px; height: auto; display: block;">
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                   <form action="{{ route('admin.payment-settings.destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus gambar QRIS ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: #ef4444; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem;">
                            Hapus Gambar
                        </button>
                    </form>
                    <span style="font-size: 0.85rem; color: #6b7280;">File: {{ basename($qrisImage) }}</span>
                </div>
            </div>
            <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 20px 0;">
        @else
            <div style="padding: 20px; background: #f3f4f6; text-align: center; border-radius: 8px; color: #6b7280; margin-bottom: 20px;">
                Belum ada gambar QRIS yang diupload.
            </div>
        @endif
    </div>

    <form action="{{ route('admin.payment-settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group" style="margin-bottom: 24px;">
            <label for="qris_image" style="display: block; margin-bottom: 8px; font-weight: 500;">
                {{ $qrisImage ? 'Ganti Gambar QRIS (Edit)' : 'Upload Gambar Baru' }}
            </label>
            <input type="file" name="qris_image" id="qris_image" class="form-input" accept="image/*" style="width: 100%; border: 1px solid #d1d5db; padding: 8px; border-radius: 6px;" required
                oninvalid="this.setCustomValidity('Harap pilih gambar QRIS.')"
                oninput="this.setCustomValidity('')">
            <p style="font-size: 0.8rem; color: #9ca3af; margin-top: 4px;">Format: JPG, PNG. Maksimal 2MB.</p>
        </div>

        <button type="submit" style="background: var(--primary-color); color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
            {{ $qrisImage ? 'Simpan Perubahan' : 'Upload Gambar' }}
        </button>
    </form>
</div>
@endsection
