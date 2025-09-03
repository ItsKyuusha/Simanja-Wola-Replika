@extends('layouts.app')

@section('page-title', 'Support')

@section('content')

  <div class="bg-white rounded-2xl p-6 mb-12 border border-gray-200">

    <div class="px-8 py-10 space-y-14">

      <!-- Support & Resources Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-12">

        <!-- Kontak Support -->
        <div class="flex flex-col items-center text-center">
          <img src="/support1.png" alt="Support" class="w-64 h-auto rounded-lg shadow-md">
          <h4 class="text-lg font-semibold mt-6">Butuh bantuan Penggunaan SIMANJA?</h4>
          <div class="mt-4 space-y-3 sm:space-y-0 sm:flex sm:justify-center sm:items-center sm:gap-6 text-blue-700">
            <a href="https://wa.me/62895360000606" target="_blank" class="flex items-center hover:underline hover:text-blue-800 transition">
              <i class="fas fa-phone-alt text-lg"></i>
              <span class="ml-2">+62895360000606</span>
            </a>
            <a href="mailto:helmiazkia2@gmail.com" target="_blank" class="flex items-center hover:underline hover:text-blue-800 transition">
              <i class="fas fa-envelope text-lg"></i>
              <span class="ml-2">helmiazkia2@gmail.com</span>
            </a>
          </div>
        </div>

        <!-- Video & Dokumentasi -->
        <div class="space-y-6">

          <!-- Video Card -->
          <div class="bg-gray-50 hover:bg-gray-100 transition duration-200 p-5 rounded-lg flex items-center gap-5 shadow-sm border border-gray-200">
            <img src="/support2.png" alt="Video Tutorial" class="w-16 h-16 object-cover rounded">
            <div>
              <h5 class="font-semibold text-md">Video Tutorial</h5>
              <p class="text-sm text-gray-600">Pelajari fitur SIMANJA melalui video panduan yang praktis.</p>
              <a href="#" class="inline-block mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded text-sm transition">
                Lihat Video
              </a>
            </div>
          </div>

          <!-- Dokumentasi Card -->
          <div class="bg-gray-50 hover:bg-gray-100 transition duration-200 p-5 rounded-lg flex items-center gap-5 shadow-sm border border-gray-200">
            <img src="/support3.png" alt="Dokumentasi SIMANJA" class="w-16 h-16 object-cover rounded">
            <div>
              <h5 class="font-semibold text-md">Dokumentasi SIMANJA</h5>
              <p class="text-sm text-gray-600">Panduan lengkap penggunaan fitur-fitur SIMANJA.</p>
              <a href="#" class="inline-block mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded text-sm transition">
                Baca Dokumentasi
              </a>
            </div>
          </div>

        </div>
      </div>

      <!-- FAQ Section -->
      <div>
        <h4 class="text-xl font-semibold mb-6">Frequently Asked Questions❓</h4>
        <div class="space-y-4">
          <!-- FAQ Item -->
          <details class="bg-gray-50 border border-gray-300 rounded-lg p-5 transition-all duration-200 open:ring-2 open:ring-blue-200">
            <summary class="font-semibold cursor-pointer text-blue-700">Bagaimana cara mengganti password akun SIMANJA?</summary>
            <p class="text-gray-700 mt-3">Klik "Lupa Password?" di halaman login, masukkan email Anda, dan ikuti instruksi reset.</p>
          </details>

          <details class="bg-gray-50 border border-gray-300 rounded-lg p-5 transition-all duration-200 open:ring-2 open:ring-blue-200">
            <summary class="font-semibold cursor-pointer text-blue-700">Bagaimana cara membuat pekerjaan untuk pegawai?</summary>
            <p class="text-gray-700 mt-3">Masuk ke halaman progress tim, klik "Tambah Pekerjaan", isi detail pekerjaan dan pilih pegawai.</p>
          </details>

          <details class="bg-gray-50 border border-gray-300 rounded-lg p-5 transition-all duration-200 open:ring-2 open:ring-blue-200">
            <summary class="font-semibold cursor-pointer text-blue-700">Bagaimana cara mengekspor pekerjaan berdasarkan bulan yang diinginkan?</summary>
            <p class="text-gray-700 mt-3">Gunakan filter bulan dan tahun di halaman progress, lalu klik "Export" untuk mengunduh laporan.</p>
          </details>
        </div>
      </div>

    </div>
  </div>

<!-- Footer -->
<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
@endpush