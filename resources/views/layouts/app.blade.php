<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>{{ ucfirst(Auth::user()->role ?? '') }} Panel | SIMANJA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="KyuuMedica - Sistem Manajemen Kesehatan" />
    <meta name="author" content="David Sugiarto" />
    <link rel="shortcut icon" href="{{ asset('logo BPS only.png') }}" type="image/x-icon" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #e3f2fd, #bbdefb);
            margin: 0;
            min-height: 100vh;
        }

        /* Navbar atas full-width */
        header.navbar-top {
            width: 100%;
            background-color: #1565c0;
            color: #e3f2fd;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 30px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        header.navbar-top .brand {
            font-weight: 700;
            font-size: 1.3rem;
            user-select: none;
        }

        header.navbar-top .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 600;
        }

        header.navbar-top .btn-logout {
            background: transparent;
            border: none;
            color: #e3f2fd;
            font-size: 1.3rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        header.navbar-top .btn-logout:hover {
            color: #ffffff;
        }

        /* Container utama: sidebar kiri + main kanan */
        .container-main {
            display: flex;
            min-height: calc(100vh - 60px); /* 60px navbar height */
            flex-wrap: nowrap;
        }

        /* Sidebar kiri */
        aside.sidebar {
            width: 260px;
            background-color: #1565c0;
            padding: 30px 20px 20px 20px;
            color: #e3f2fd;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Sidebar Logo */
        .sidebar .logo {
            max-width: 180px;
            margin: 0 auto 30px auto;
            display: block;
        }

        /* Sidebar Heading */
        .sidebar h4 {
            font-weight: 600;
            color: #ffffff;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Sidebar Navigation */
        .sidebar .nav-link {
            transition: all 0.2s ease;
            padding: 10px 15px;
            border-radius: 10px;
            color: #e3f2fd;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .nav-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar .nav-link:hover {
            background-color: #1e88e5;
            color: #fff;
        }

        .sidebar .nav-link.active {
            background-color: #ffffff;
            color: #1565c0 !important;
            font-weight: 600;
        }

        /* Main content kanan */
        .main-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
            box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.05);
            min-width: 0;
        }

        /* Area atas di main content */
        .main-top-area {
            padding: 20px 30px;
            background-color: #f5f5f5;
            border-bottom: 1px solid #ddd;
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
        }

        /* Main content area */
        main.main-content {
            padding: 40px 30px;
            flex-grow: 1;
            overflow-y: auto;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .container-main {
                flex-direction: column;
                min-height: auto;
            }

            aside.sidebar {
                width: 100%;
                height: auto;
                padding: 20px;
                box-shadow: none;
            }

            .main-container {
                order: 2;
            }

            .main-top-area {
                padding: 15px 20px;
                font-size: 1rem;
            }

            main.main-content {
                padding: 20px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="container-main">
        <!-- Sidebar kiri -->
        <aside class="sidebar">
            <img src="{{ asset('logo.png') }}" alt="KyuuMedica Logo" class="logo" />

            <h4>{{ ucfirst(Auth::user()->role ?? 'Guest') }} Dashboard</h4>
            <hr class="text-light" />

            <!-- Navigation Menu -->
            <ul class="nav flex-column">
                @auth
                    @if (Auth::user()->role === 'admin')
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}"
                                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.progress') }}"
                                class="nav-link {{ request()->routeIs('admin.progress') ? 'active' : '' }}">
                                <i class="fas fa-chart-line"></i> Progress
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.pekerjaan') }}"
                                class="nav-link {{ request()->routeIs('admin.pekerjaan') ? 'active' : '' }}">
                                <i class="fas fa-briefcase"></i> Pekerjaan
                            </a>
                        </li>

                        <li class="nav-item has-treeview {{ request()->is('master*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link d-flex align-items-center {{ request()->is('master*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p class="mb-0 flex-grow-1"> Master</p>
                                <i class="right fas fa-angle-left"></i>
                            </a>
                            <ul class="nav nav-treeview ps-4">
                                <li class="nav-item">
                                    <a href="{{ route('admin.masterpegawai') }}"
                                        class="nav-link d-flex align-items-center {{ request()->routeIs('admin.masterpegawai') ? 'active' : '' }}">
                                        <i class="fas fa-users nav-icon me-2"></i>
                                        <p class="mb-0">Pegawai</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link {{ request()->routeIs('support') ? 'active' : '' }}">
                                <i class="fas fa-life-ring"></i> Support
                            </a>
                        </li>
                    @elseif (Auth::user()->role === 'superadmin')
                        <li class="nav-item">
                            <a href="{{ route('superadmin.dashboard') }}"
                                class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('superadmin.progress') }}"
                                class="nav-link {{ request()->routeIs('superadmin.progress') ? 'active' : '' }}">
                                <i class="fas fa-chart-line"></i> Progress
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('superadmin.pekerjaan') }}"
                                class="nav-link {{ request()->routeIs('superadmin.pekerjaan') ? 'active' : '' }}">
                                <i class="fas fa-briefcase"></i> Pekerjaan
                            </a>
                        </li>

                        <li class="nav-item has-treeview {{ request()->is('master*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link d-flex align-items-center {{ request()->is('master*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p class="mb-0 flex-grow-1"> Master</p>
                                <i class="right fas fa-angle-left"></i>
                            </a>
                            <ul class="nav nav-treeview ps-4">
                                <li class="nav-item">
                                    <a href="{{ route('superadmin.masteruser') }}"
                                        class="nav-link d-flex align-items-center {{ request()->routeIs('superadmin.masteruser') ? 'active' : '' }}">
                                        <i class="fas fa-users nav-icon me-2"></i>
                                        <p class="mb-0">User</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('superadmin.masterjenispekerjaan') }}"
                                        class="nav-link d-flex align-items-center {{ request()->routeIs('superadmin.masterjenispekerjaan') ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon me-2"></i>
                                        <p class="mb-0">Jenis Pekerjaan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('superadmin.masterpegawai') }}"
                                        class="nav-link d-flex align-items-center {{ request()->routeIs('superadmin.masterpegawai') ? 'active' : '' }}">
                                        <i class="fas fa-user-tie nav-icon me-2"></i>
                                        <p class="mb-0">Pegawai</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('superadmin.masterjenistim') }}"
                                        class="nav-link d-flex align-items-center {{ request()->routeIs('superadmin.masterjenistim') ? 'active' : '' }}">
                                        <i class="fas fa-users-cog nav-icon me-2"></i>
                                        <p class="mb-0">Jenis Tim</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('superadmin.support') }}" class="nav-link {{ request()->routeIs('superadmin.support') ? 'active' : '' }}">
                                <i class="fas fa-life-ring"></i> Support
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>
        </aside>

        <!-- Main content kanan -->
        <div class="main-container">
            <div class="main-top-area d-flex justify-content-between align-items-center">
                <div>
                    Selamat datang di SIMANJA, Sistem Manajemen Kerja !
                </div>
                <div class="user-info d-flex align-items-center gap-2">
                    <strong>{{ ucfirst(Auth::user()->nama ?? 'User') }}</strong>
                    <form action="{{ route('logout') }}" method="POST" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light text-dark" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
            <main class="main-content">
                @yield('content')
            </main>
            <footer class="main-footer text-center py-3 bg-light border-top">
                <div class="container">
                    <span class="text-muted">© {{ date('Y') }} SIMANJA - Sistem Manajemen Kerja. All rights reserved.</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @elseif (session('error'))
            toastr.error("{{ session('error') }}");
        @elseif (session('warning'))
            toastr.warning("{{ session('warning') }}");
        @elseif (session('info'))
            toastr.info("{{ session('info') }}");
        @endif
    </script>

    <script>
        document.querySelectorAll('.has-treeview > a').forEach((menu) => {
            menu.addEventListener('click', function (e) {
                e.preventDefault();
                const parent = this.parentElement;
                parent.classList.toggle('menu-open');
                const submenu = parent.querySelector('.nav-treeview');
                if (submenu) {
                    submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
                }
            });

            const parent = menu.parentElement;
            const submenu = parent.querySelector('.nav-treeview');
            if (parent.classList.contains('menu-open') && submenu) {
                submenu.style.display = 'block';
            } else if (submenu) {
                submenu.style.display = 'none';
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
