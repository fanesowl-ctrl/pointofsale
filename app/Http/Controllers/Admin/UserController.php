<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar kasir (users)
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Fitur Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('username', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
        }

        $perPage = (int) $request->input('per_page', 10);
        
        // Exclude admin sendiri dari list jika mau, tapi biarkan dulu
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Simpan Kasir Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
        ], [
            'username.unique' => 'Username sudah digunakan.',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username . '@system.local', // Dummy email required by Laravel User model
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Kasir berhasil ditambahkan.');
    }

    /**
     * Update Kasir
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username . '@system.local',
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data kasir berhasil diperbarui.');
    }

    /**
     * Hapus Kasir
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id == auth()->id()) {
            return redirect()->back()->withErrors(['Tidak bisa menghapus akun sendiri.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Kasir berhasil dihapus.');
    }
}
