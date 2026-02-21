<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Admin POS</title>
    <link rel="stylesheet" href="{{ asset('css/admin-auth.css') }}">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="brand-section">
                <div class="brand-logo">
                    <i class="ri-shopping-bag-3-fill"></i>
                </div>
                <h1 class="brand-title">Point of Sale</h1>
                <p class="brand-subtitle">Silakan masuk untuk melanjutkan</p>
            </div>

            @if (session('success'))
                <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="error-message">
                    <ul style="list-style: none; padding: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Email / Username</label>
                    <input type="text" id="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus placeholder="Email Admin atau Username Kasir"
                        oninvalid="this.setCustomValidity('Harap isi bidang ini dengan email atau username.')"
                        oninput="this.setCustomValidity('')">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required
                        oninvalid="this.setCustomValidity('Harap isi bidang ini dengan password.')"
                        oninput="this.setCustomValidity('')">
                </div>

                <button type="submit" class="btn-primary">
                    Masuk ke Dashboard
                </button>
            </form>
        </div>
    </div>
</body>
</html>
