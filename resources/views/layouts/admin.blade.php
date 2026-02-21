<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - POS System</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                    <i class="ri-shopping-bag-3-fill"></i>
                    <span>POS Admin</span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-label">Menu Utama</div>
                
                <div class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="ri-dashboard-line nav-icon"></i>
                        <span>Home</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.products.index') }}" class="nav-link {{ (request()->routeIs('admin.products.*') && !request()->routeIs('admin.products.discounted')) ? 'active' : '' }}">
                        <i class="ri-box-3-line nav-icon"></i>
                        <span>Data Barang</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.products.discounted') }}" class="nav-link {{ request()->routeIs('admin.products.discounted') ? 'active' : '' }}">
                        <i class="ri-price-tag-3-line nav-icon"></i>
                        <span>Barang Diskon</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.stocks.index') }}" class="nav-link {{ request()->routeIs('admin.stocks.*') ? 'active' : '' }}">
                        <i class="ri-archive-line nav-icon"></i>
                        <span>Manajemen Stok</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.members.index') }}" class="nav-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                        <i class="ri-user-star-line nav-icon"></i>
                        <span>Data Member</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="ri-file-chart-line nav-icon"></i>
                        <span>Laporan Bulanan</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.kasirs.index') }}" class="nav-link {{ request()->routeIs('admin.kasirs.*') ? 'active' : '' }}">
                        <i class="ri-user-settings-line nav-icon"></i>
                        <span>Data Kasir</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.payment-settings.index') }}" class="nav-link {{ request()->routeIs('admin.payment-settings.*') ? 'active' : '' }}">
                        <i class="ri-qr-code-line nav-icon"></i>
                        <span>Pengaturan Pembayaran</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar">
                        {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->name ?? 'Admin' }}</span>
                        <span class="user-role">Administrator</span>
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
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
                <!-- Bisa diisi notifikasi atau item header lainnya -->
                <div style="font-size: 0.9rem; color: #6b7280;">
                    {{ date('l, d F Y') }}
                </div>
            </header>

            <div class="page-content">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
