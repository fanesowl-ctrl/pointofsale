<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kasir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class KasirController extends Controller
{
    /**
     * Menampilkan daftar kasir
     */
    public function index(Request $request)
    {
        $query = Kasir::query();

        // Fitur Search (Username/Nama)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('username', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
        }

        $perPage = (int) $request->input('per_page', 10);
        
        $kasirs = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('admin.kasirs.index', compact('kasirs'));
    }

    /**
     * Simpan Kasir Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:kasir,username',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.'
        ]);

        Kasir::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password, // Disimpan Plain Text (Tanpa Hash)
        ]);

        return redirect()->route('admin.kasirs.index')->with('success', 'Kasir berhasil ditambahkan.');
    }

    /**
     * Update Kasir
     */
    public function update(Request $request, $id)
    {
        $kasir = Kasir::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('kasir')->ignore($kasir->id)],
        ], [
            'username.unique' => 'Username sudah digunakan.',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $data['password'] = $request->password; // Disimpan Plain Text
        }

        $kasir->update($data);

        return redirect()->route('admin.kasirs.index')->with('success', 'Data Kasir berhasil diperbarui.');
    }

    /**
     * Hapus Kasir
     */
    public function destroy($id)
    {
        $kasir = Kasir::findOrFail($id);
        $kasir->delete();

        return redirect()->route('admin.kasirs.index')->with('success', 'Kasir berhasil dihapus.');
    }
}
