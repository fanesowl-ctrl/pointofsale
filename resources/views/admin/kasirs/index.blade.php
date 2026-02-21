@extends('layouts.admin')

@section('title', 'Data Kasir')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h1 class="page-title" style="margin-bottom: 0;">Data Kasir</h1>
    <div style="display: flex; gap: 10px;">
        <button onclick="openModal('addModal')" class="btn-primary" style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="ri-user-add-line"></i> Tambah Kasir
        </button>
    </div>
</div>

@if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <form action="{{ route('admin.kasirs.index') }}" method="GET" style="display: flex; align-items: center; gap: 10px;">
            <label>Show</label>
            <select name="per_page" onchange="this.form.submit()" style="padding: 6px; border-radius: 6px; border: 1px solid #d1d5db;">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            </select>
            <label>entries</label>
        </form>

        <form action="{{ route('admin.kasirs.index') }}" method="GET">
            <input type="text" name="search" placeholder="Cari username..." value="{{ request('search') }}" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; width: 250px;">
        </form>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f9fafb; text-align: left;">
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">No</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Nama</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Username</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Password</th>
                <th style="padding: 12px; border-bottom: 2px solid #e5e7eb;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kasirs as $key => $kasir)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px;">{{ $kasirs->firstItem() + $key }}</td>
                <td style="padding: 12px; font-weight: 500;">{{ $kasir->name }}</td>
                <td style="padding: 12px;">
                    <span style="background: #eef2ff; color: #4f46e5; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">
                        {{ $kasir->username }}
                    </span>
                </td>
                <td style="padding: 12px; font-family: monospace; font-size: 0.85rem; color: #4b5563;">
                    {{ $kasir->password }}
                </td>
                <td style="padding: 12px;">
                    <button type="button" onclick='editKasir(@json($kasir))' style="background: #f59e0b; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; margin-right: 4px;">
                        <i class="ri-pencil-line"></i>
                    </button>
                    
                    <form action="{{ route('admin.kasirs.destroy', $kasir->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus kasir ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer;">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding: 24px; text-align: center; color: #6b7280;">Tidak ada data kasir.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $kasirs->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="addModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100; justify-content: center; align-items: center;">
    <div class="modal-content" style="background: white; width: 450px; padding: 24px; border-radius: 16px;">
        <h2 id="modalTitle" style="margin-bottom: 20px; font-size: 1.5rem;">Tambah Kasir</h2>
        <form id="kasirForm" action="{{ route('admin.kasirs.store') }}" method="POST">
            @csrf
            <div id="methodField"></div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Nama Lengkap</label>
                <input type="text" id="name" name="name" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                    oninvalid="this.setCustomValidity('Harap isi nama lengkap.')"
                    oninput="this.setCustomValidity('')">
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Username</label>
                <input type="text" id="username" name="username" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                    oninvalid="this.setCustomValidity('Harap isi username.')"
                    oninput="this.setCustomValidity('')">
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Password</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Isi untuk mengubah password" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;"
                    oninvalid="this.setCustomValidity('Harap isi password.')"
                    oninput="this.setCustomValidity('')">
                <small id="passwordHint" style="color: #6b7280; font-size: 0.8rem; display: none;">Kosongkan jika tidak ingin mengubah.</small>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('addModal')" style="padding: 10px 20px; background: #e5e7eb; border: none; border-radius: 8px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
    document.getElementById('kasirForm').action = "{{ route('admin.kasirs.store') }}";
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('modalTitle').innerText = 'Tambah Kasir';
    document.getElementById('name').value = '';
    document.getElementById('username').value = '';
    document.getElementById('password').required = true;
    document.getElementById('passwordHint').style.display = 'none';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function editKasir(kasir) {
    const modal = document.getElementById('addModal');
    modal.style.display = 'flex';
    
    document.getElementById('modalTitle').innerText = 'Edit Kasir';
    document.getElementById('kasirForm').action = "/admin/kasirs/" + kasir.id;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    
    document.getElementById('name').value = kasir.name;
    document.getElementById('username').value = kasir.username;
    
    document.getElementById('password').required = false;
    document.getElementById('passwordHint').style.display = 'block';
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.style.display = 'none';
    }
}
</script>
@endsection
