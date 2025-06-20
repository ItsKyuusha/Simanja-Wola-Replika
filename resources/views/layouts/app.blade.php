<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ ucfirst(Auth::user()->role ?? '') }} Panel | KyuuMedica</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="KyuuMedica - Sistem Manajemen Kesehatan">
    <meta name="author" content="David Sugiarto">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #e3f2fd, #bbdefb);
            margin: 0;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            background-color: #1565c0;
            padding: 30px 20px;
            color: #e3f2fd;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
        }

        .sidebar h4 {
            font-weight: 600;
            color: #ffffff;
        }

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
        }

        .sidebar .nav-link.active {
            background-color: #ffffff;
            color: #1565c0 !important;
            font-weight: 600;
        }

        .logout-btn {
            position: absolute;
            bottom: 30px;
            left: 20px;
            right: 20px;
        }

        .logout-btn button {
            background-color: transparent;
            border: 2px solid #e3f2fd;
            color: #e3f2fd;
            font-weight: 500;
        }

        .logout-btn button:hover {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .main-content {
            background-color: #ffffff;
            padding: 40px;
            flex-grow: 1;
            min-height: 100vh;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
            }

            .logout-btn {
                position: relative;
                margin-top: 20px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="d-flex flex-wrap">

        <!-- Sidebar -->
        <aside class="sidebar position-relative">
            <div class="text-center mb-4">
                <img src="{{ asset('logo.png') }}" alt="KyuuMedica Logo" style="max-width: 180px;">
            </div>

            <h4 class="text-center mb-3">{{ ucfirst(Auth::user()->role ?? 'Guest') }} Dashboard</h4>
            <hr class="text-light mb-4">

            <!-- Navigation Menu -->
            <ul class="nav flex-column">
                @auth
                    @if(Auth::user()->role === 'admin')
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link {{ request()->routeIs('progress') ? 'active' : '' }}">
                                <i class="fas fa-chart-line"></i> Progress
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link {{ request()->routeIs('pekerjaan') ? 'active' : '' }}">
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
                                    <a href="" class="nav-link d-flex align-items-center {{ request()->routeIs('') ? 'active' : '' }}">
                                        <i class="fas fa-users nav-icon me-2"></i>
                                        <p class="mb-0">Pegawai</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="" class="nav-link d-flex align-items-center {{ request()->routeIs('') ? 'active' : '' }}">
                                        <i class="fas fa-user-cog nav-icon me-2"></i>
                                        <p class="mb-0">Pengurus</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link {{ request()->routeIs('support') ? 'active' : '' }}">
                                <i class="fas fa-life-ring"></i> Support
                            </a>
                        </li>
                    @elseif(Auth::user()->role === 'superadmin')
                        <li class="nav-item">
                            <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="" class="nav-link {{ request()->routeIs('') ? 'active' : '' }}">
                                <i class="fas fa-chart-line"></i> Progress
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="" class="nav-link {{ request()->routeIs('') ? 'active' : '' }}">
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
                                    <a href="" class="nav-link d-flex align-items-center {{ request()->routeIs('') ? 'active' : '' }}">
                                        <i class="fas fa-users nav-icon me-2"></i>
                                        <p class="mb-0">User</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="" class="nav-link d-flex align-items-center {{ request()->routeIs('') ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon me-2"></i>
                                        <p class="mb-0">Jenis Pekerjaan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="" class="nav-link d-flex align-items-center {{ request()->routeIs('') ? 'active' : '' }}">
                                        <i class="fas fa-user-tie nav-icon me-2"></i>
                                        <p class="mb-0">Pegawai</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="" class="nav-link d-flex align-items-center {{ request()->routeIs('') ? 'active' : '' }}">
                                        <i class="fas fa-users-cog nav-icon me-2"></i>
                                        <p class="mb-0">Jenis Tim</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="" class="nav-link {{ request()->routeIs('') ? 'active' : '' }}">
                                <i class="fas fa-life-ring"></i> Support
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- Logout Button -->
            @auth
            <div class="logout-btn">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn w-100">Logout</button>
                </form>
            </div>
            @endauth
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @elseif(session('error'))
            toastr.error("{{ session('error') }}");
        @elseif(session('warning'))
            toastr.warning("{{ session('warning') }}");
        @elseif(session('info'))
            toastr.info("{{ session('info') }}");
        @endif
    </script>

    <script>
  document.querySelectorAll('.has-treeview > a').forEach(menu => {
    menu.addEventListener('click', function(e) {
      e.preventDefault();
      const parent = this.parentElement;

      // Toggle menu-open class
      parent.classList.toggle('menu-open');

      // Optionally toggle submenu visibility
      const submenu = parent.querySelector('.nav-treeview');
      if (submenu) {
        if (submenu.style.display === 'block') {
          submenu.style.display = 'none';
        } else {
          submenu.style.display = 'block';
        }
      }
    });

    // Initialize submenu display based on menu-open class
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
