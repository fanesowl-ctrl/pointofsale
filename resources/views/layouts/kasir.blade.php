<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kasir Dashboard') - POS System</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('kasir.dashboard') }}" class="sidebar-brand">
                    <i class="ri-store-3-fill"></i>
                    <span>POS Kasir</span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-label">Menu Kasir</div>
                
                <div class="nav-item">
                    <a href="{{ route('kasir.dashboard') }}" class="nav-link {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
                        <i class="ri-home-4-line nav-icon"></i>
                        <span>Home</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('kasir.transaksi.index') }}" class="nav-link {{ request()->routeIs('kasir.transaksi.*') ? 'active' : '' }}">
                        <i class="ri-shopping-cart-line nav-icon"></i>
                        <span>Transaksi</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('kasir.laporan.index') }}" class="nav-link {{ request()->routeIs('kasir.laporan.*') ? 'active' : '' }}">
                        <i class="ri-file-chart-line nav-icon"></i>
                        <span>Laporan</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar" style="background-color: var(--secondary-color, #10b981);">
                        {{ substr(session('kasir_name') ?? 'K', 0, 1) }}
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ session('kasir_name') ?? 'Kasir' }}</span>
                        <span class="user-role">Kasir</span>
                    </div>
                    <form action="{{ route('kasir.logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background:none; border:none; cursor:pointer; color: #9ca3af;" title="Logout">
                            <i class="ri-logout-box-r-line"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div style="font-size: 0.9rem; color: #6b7280;">
                    {{ date('l, d F Y') }}
                </div>
            </header>

            <div class="page-content">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
