<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Admin POS</title>
    <link rel="stylesheet" href="{{ asset('css/admin-auth.css') }}">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="brand-section">
                <div class="brand-logo">
                    <i class="ri-user-add-fill"></i>
                </div>
                <h1 class="brand-title">Daftar Akun</h1>
                <p class="brand-subtitle">Buat akun admin baru</p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    <ul style="list-style: none; padding: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.register.submit') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="John Doe" value="{{ old('name') }}" required autofocus
                        oninvalid="this.setCustomValidity('Harap isi nama lengkap.')"
                        oninput="this.setCustomValidity('')">
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="admin@example.com" value="{{ old('email') }}" required
                        oninvalid="this.setCustomValidity('Harap isi alamat email.')"
                        oninput="this.setCustomValidity('')">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required
                        oninvalid="this.setCustomValidity('Harap isi password.')"
                        oninput="this.setCustomValidity('')">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="••••••••" required
                        oninvalid="this.setCustomValidity('Harap konfirmasi password.')"
                        oninput="this.setCustomValidity('')">
                </div>

                <button type="submit" class="btn-primary">
                    Daftar
                </button>
            </form>

            <div class="footer-text">
                Sudah punya akun? <a href="{{ route('admin.login') }}" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">Masuk disini</a>
            </div>
        </div>
    </div>
</body>
</html>
