@extends('layouts.app')

@section('title', 'Support - SIMANJA')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="bg-blue-700 text-white text-xl font-semibold px-6 py-4 rounded-t-xl">
            Halaman Support
        </div>
        <div class="px-6 py-4">
            <!-- Support Info Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Contact Card -->
                <div class="flex flex-col items-center text-center">
                    <img src="/support1.png" alt="Support Image" class="w-64 h-auto rounded">
                    <h4 class="text-lg font-semibold mt-4">Butuh bantuan Penggunaan SIMANJA?</h4>
                    <div class="flex flex-col sm:flex-row justify-center gap-4 mt-4 text-blue-600">
                        <a href="https://wa.me/6281229429025" target="_blank" class="flex items-center hover:underline">
                            <i class="fas fa-phone text-xl"></i>
                            <span class="ml-2">+62 812 2942 9025</span>
                        </a>
                        <a href="mailto:kyuushaxyz@gmail.com" target="_blank" class="flex items-center hover:underline">
                            <i class="fas fa-envelope text-xl"></i>
                            <span class="ml-2">kyuushaxyz@gmail.com</span>
                        </a>
                    </div>
                </div>

                <!-- Video & Doc -->
                <div class="space-y-6">
                    <!-- Video Tutorial -->
                    <div class="bg-gray-100 p-4 rounded-lg flex items-center gap-4 shadow">
                        <img src="/support2.png" alt="Video Tutorial" class="w-16 h-16 object-cover">
                        <div class="flex-1">
                            <h5 class="font-bold text-md">Video Tutorial</h5>
                            <p class="text-sm text-gray-600">Tonton video tutorial penggunaan SIMANJA untuk mempelajari fitur-fitur utama.</p>
                            <a href="#" class="inline-block mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded text-sm">Lihat Video</a>
                        </div>
                    </div>

                    <!-- Dokumentasi -->
                    <div class="bg-gray-100 p-4 rounded-lg flex items-center gap-4 shadow">
                        <img src="/support3.png" alt="Documentation" class="w-16 h-16 object-cover">
                        <div class="flex-1">
                            <h5 class="font-bold text-md">Dokumentasi Penggunaan SIMANJA</h5>
                            <p class="text-sm text-gray-600">Baca dokumentasi lengkap mengenai cara penggunaan SIMANJA untuk memaksimalkan penggunaan sistem.</p>
                            <a href="#" class="inline-block mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded text-sm">Baca Dokumentasi</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="mt-10">
                <h4 class="text-xl font-semibold mb-4">❓ Frequently Asked Questions</h4>
                <div class="space-y-4">
                    <!-- FAQ Item 1 -->
                    <details class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                        <summary class="font-semibold cursor-pointer text-blue-700">Bagaimana cara mengganti password akun SIMANJA?</summary>
                        <p class="text-gray-700 mt-2">Untuk mengganti password akun, klik "Lupa Password?" di halaman login, masukkan email akun Anda, dan ikuti instruksi yang diberikan.</p>
                    </details>

                    <!-- FAQ Item 2 -->
                    <details class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                        <summary class="font-semibold cursor-pointer text-blue-700">Bagaimana cara membuat pekerjaan untuk pegawai?</summary>
                        <p class="text-gray-700 mt-2">Klik "Tambah Pekerjaan" di halaman progress tim ketua, pilih jenis pekerjaan dan target waktu, lalu pilih pegawai yang bertugas.</p>
                    </details>

                    <!-- FAQ Item 3 -->
                    <details class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                        <summary class="font-semibold cursor-pointer text-blue-700">Bagaimana cara mengekspor pekerjaan berdasarkan bulan yang diinginkan?</summary>
                        <p class="text-gray-700 mt-2">Pilih bulan dan tahun yang diinginkan di halaman progress tim ketua, klik tombol "Filter", kemudian pilih "Export" untuk mengunduh data.</p>
                    </details>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
@endpush
