<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="shortcut icon" href="{{ asset('logo BPS only.png') }}" type="image/x-icon" />
  <title>Login - SIMANJA</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#FACC15',
          }
        }
      }
    }
  </script>

  <!-- Alpine.js (jika nanti ingin pakai modal selamat datang) -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- Custom Animation -->
  <style>
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }

    .fade-in {
      animation: fadeIn 0.5s ease-out forwards;
    }
  </style>
</head>

<body class="bg-gradient-to-br from-blue-800 via-blue-600 to-purple-800 min-h-screen flex items-center justify-center relative">

  <!-- Loading Overlay Component -->
  <x-loading-overlay />

  <!-- Logo kiri atas mobile -->
  <div class="absolute top-4 left-4 md:hidden z-20">
    <img src="{{ asset('logo.png') }}" alt="Logo" class="w-24">
  </div>

  <!-- Kontainer Utama -->
  <div class="w-full max-w-6xl mx-auto px-4 md:px-0 flex flex-col md:grid md:grid-cols-2 items-center justify-center md:justify-start gap-y-4 md:gap-6 fade-in relative z-10 min-h-screen md:min-h-0">

    <!-- Kolom Kiri: Informasi -->
    <div class="text-white pt-2 px-8 space-y-4 flex flex-col justify-center items-center text-center md:items-start md:text-left">
      <img src="{{ asset('logo.png') }}" alt="Logo" class="w-48 hidden md:block">
      <h2 class="text-3xl font-bold">Selamat Datang di <span class="text-primary">WOLA</span></h2>
      <p class="text-lg leading-relaxed hidden md:block max-w-xl">
        Platform manajemen kinerja untuk BPS Kota Semarang. Membantu memantau dan meningkatkan kinerja secara efektif, efisien, dan transparan.
      </p>
    </div>

    <!-- Kolom Kanan: Form Login -->
    <div class="backdrop-blur-md bg-white/20 border border-white/30 shadow-lg rounded-xl p-6 md:p-10 w-full max-w-md mx-auto text-white">
      <h2 class="text-2xl font-bold mb-6 text-center">Masuk ke Akun Anda</h2>

      @if($errors->any())
        <div class="bg-red-100 text-red-700 text-sm px-4 py-2 rounded mb-4">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" onsubmit="showLoading()" class="space-y-5">
        @csrf

        <div>
          <label for="email" class="block mb-1 font-medium text-white">Email</label>
          <input type="email" id="email" name="email" required autofocus
                 class="w-full px-4 py-2 rounded bg-white/80 text-gray-800 border focus:outline-none focus:ring-2 focus:ring-yellow-400 transition"
                 placeholder="nama@bps.go.id" />
        </div>

        <div>
          <label for="password" class="block mb-1 font-medium text-white">Password</label>
          <input type="password" id="password" name="password" required
                 class="w-full px-4 py-2 rounded bg-white/80 text-gray-800 border focus:outline-none focus:ring-2 focus:ring-yellow-400 transition"
                 placeholder="***********" />
        </div>

        <button type="submit"
                class="w-full bg-primary hover:bg-yellow-500 text-gray-800 font-bold py-2 px-4 rounded transition duration-300 shadow-md hover:shadow-lg">
          Login
        </button>
      </form>

      <p class="mt-6 text-sm text-center text-white">
        Lupa Password? <a href="https://wa.me/62895360000606" target="_blank"
                          class="underline hover:text-primary">Hubungi Admin via WhatsApp</a>
      </p>
    </div>
  </div>

  <!-- JS untuk loading -->
  <script>
    function showLoading() {
      const overlay = document.getElementById('loadingOverlay');
      if (overlay) {
        overlay.classList.remove('hidden');
      }
    }
  </script>
</body>

</html>
