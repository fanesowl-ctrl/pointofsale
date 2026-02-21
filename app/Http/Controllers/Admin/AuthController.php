<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'email' => ['required'], // Email (Admin) atau Username (Kasir)
            'password' => ['required'],
        ], [
            'email.required' => 'Email atau Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // 2. Coba Login sebagai ADMIN (Auth Manual Check Plain Text)
        // Kita asumsikan input admin berbentuk Email
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            // Cari user by email
            $user = \App\Models\User::where('email', $request->email)->first();
            
            // Cek Password TEXT biasa (Tanpa Hash) sesusai keinginan user
            if ($user && $user->password === $request->password) {
                Auth::login($user); // Login manual
                $request->session()->regenerate();
                return redirect()->intended('admin/dashboard')->with('success', 'Login berhasil! Selamat datang ' . $user->name . '.');
            }
        }

        // 3. Coba Login sebagai KASIR (Manual Check)
        // Input dianggap sebagai Username
        $kasir = \App\Models\Kasir::where('username', $request->email)->first();

        if ($kasir && $kasir->password === $request->password) {
            // Login Kasir Sukses
            // Simpan identitas kasir di session
            session([
                'kasir_id' => $kasir->id,
                'kasir_name' => $kasir->name,
                'role' => 'kasir',
                'is_kasir_logged_in' => true
            ]);
            
            return redirect()->route('kasir.dashboard')->with('success', 'Login berhasil! Selamat datang ' . $kasir->name . '.');
        }

        // 4. Jika Gagal Semua
        return back()->withErrors([
            'email' => 'Login gagal. Cek Email/Username dan Password.',
        ]);
    }

    public function logout(Request $request)
    {
        // Logout Admin (Auth Facade)
        Auth::logout();

        // Hapus semua session (Valid untuk Admin maupun Kasir)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    public function showRegisterForm()
    {
        return view('admin.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Simpan Plain Text sesuai request
        ]);

        // Auth::login($user); // Disable auto login users

        return redirect()->route('admin.login')->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
    }
}
