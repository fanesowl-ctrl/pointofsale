@extends('layouts.admin')

@section('title', 'Kelola Member')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="margin-bottom: 4px;">Data Member</h1>
        <p style="color: #6b7280; font-size: 0.9rem;">Kelola pelanggan setia dan diskon member.</p>
    </div>
    <button onclick="openCreateModal()" class="btn-primary" style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
        <i class="ri-user-add-line"></i> Tambah Member
    </button>
</div>

<!-- Flash Message -->
@if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<!-- Search & Filter -->
<div class="card" style="margin-bottom: 24px; padding: 16px;">
    <form action="{{ route('admin.members.index') }}" method="GET" style="display: flex; justify-content: flex-end;">
        <input type="text" name="search" placeholder="Cari nama atau kode member..." value="{{ request('search') }}" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; width: 250px;">
    </form>
</div>

<!-- Table -->
<div class="card" style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
        <thead>
            <tr style="border-bottom: 2px solid #f3f4f6; text-align: left;">
                <th style="padding: 16px; font-weight: 600; color: #374151;">Kode Member</th>
                <th style="padding: 16px; font-weight: 600; color: #374151;">Nama</th>
                <th style="padding: 16px; font-weight: 600; color: #374151;">Telepon</th>
                <th style="padding: 16px; font-weight: 600; color: #374151;">Diskon (%)</th>
                <th style="padding: 16px; font-weight: 600; color: #374151;">Status</th>
                <th style="padding: 16px; font-weight: 600; color: #374151;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
            <tr style="border-bottom: 1px solid #f3f4f6; hover: background-color: #f9fafb;">
                <td style="padding: 16px; font-family: monospace; color: #4b5563;">{{ $member->code }}</td>
                <td style="padding: 16px; font-weight: 500; color: #1f2937;">{{ $member->name }}</td>
                <td style="padding: 16px; color: #4b5563;">{{ $member->phone ?? '-' }}</td>
                <td style="padding: 16px; color: #dc2626; font-weight: 700;">{{ $member->discount_percentage }}%</td>
                <td style="padding: 16px;">
                    @if($member->is_active)
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: #d1fae5; color: #065f46;">Aktif</span>
                    @else
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: #f3f4f6; color: #6b7280;">Non-Aktif</span>
                    @endif
                </td>
                <td style="padding: 16px;">
                    <button onclick='openEditModal(@json($member))' style="background: none; border: none; cursor: pointer; color: #d97706; margin-right: 8px;" title="Edit">
                        <i class="ri-pencil-line" style="font-size: 1.2rem;"></i>
                    </button>
                    <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus member ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; cursor: pointer; color: #ef4444;" title="Hapus">
                            <i class="ri-delete-bin-line" style="font-size: 1.2rem;"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">Belum ada data member.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding: 16px;">
        {{ $members->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal Create/Edit -->
<div id="memberModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100; justify-content: center; align-items: center;">
    <div class="modal-content" style="background: white; width: 500px; padding: 24px; border-radius: 16px; max-width: 90%;">
        <h2 id="modalTitle" style="margin-bottom: 20px; font-size: 1.25rem;">Tambah Member</h2>
        <form id="memberForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Kode Member</label>
                <input type="text" name="code" id="code" class="form-input" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" placeholder="Contoh: MBR-001">
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Nama Member</label>
                <input type="text" name="name" id="name" class="form-input" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">No. Telepon (Opsional)</label>
                <input type="text" name="phone" id="phone" class="form-input" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">Diskon (%)</label>
                <input type="number" name="discount_percentage" id="discount_percentage" min="0" max="100" step="0.1" class="form-input" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
                <small style="color: #6b7280;">Masukkan angka persen, misal 10 untuk 10%.</small>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked style="width: 18px; height: 18px;"> 
                    <span style="font-weight: 500;">Status Aktif</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeModal()" style="padding: 10px 20px; background: #e5e7eb; border: none; border-radius: 8px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Member';
        document.getElementById('memberForm').action = "{{ route('admin.members.store') }}";
        document.getElementById('methodField').value = 'POST';
        
        // Reset form
        document.getElementById('code').value = 'MBR-' + Math.floor(100000 + Math.random() * 900000); // Random 6 digit
        document.getElementById('name').value = '';
        document.getElementById('phone').value = '';
        document.getElementById('discount_percentage').value = '0';
        document.getElementById('is_active').checked = true;
        
        document.getElementById('memberModal').style.display = 'flex';
    }

    function openEditModal(member) {
        document.getElementById('modalTitle').innerText = 'Edit Member';
        document.getElementById('memberForm').action = "/admin/members/" + member.id;
        document.getElementById('methodField').value = 'PUT';
        
        document.getElementById('code').value = member.code;
        document.getElementById('name').value = member.name;
        document.getElementById('phone').value = member.phone;
        document.getElementById('discount_percentage').value = member.discount_percentage;
        document.getElementById('is_active').checked = member.is_active ? true : false;
        
        document.getElementById('memberModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('memberModal').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target.id === 'memberModal') {
            closeModal();
        }
    }
</script>
@endsection
