@extends('layouts.app')
@section('page-title', 'Pegawai')

@section('content')
<div class="space-y-10">

  {{-- ================== DAFTAR PEGAWAI TIM ================== --}}
  <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
      <h2 class="text-2xl font-semibold text-blue-600">
        Daftar Pegawai Dalam Tim Anda
      </h2>

      <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
        <!-- Form Search Pegawai Dalam Tim -->
        <form method="GET" action="{{ route('admin.pegawai.index') }}" class="flex gap-3 w-full sm:w-auto">
          <input type="text" name="search_tim" value="{{ request('search_tim') }}"
            class="px-4 py-2 w-full sm:w-64 border border-gray-300 rounded-lg 
                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                   bg-white/50 backdrop-blur-sm placeholder-gray-500"
            placeholder="Cari pegawai tim...">
          <button type="submit"
            class="px-4 py-2 rounded-lg border border-gray-400 text-gray-600 font-medium 
                   bg-white/40 backdrop-blur-sm hover:bg-gray-100 hover:text-gray-700
                   transition duration-200 ease-in-out transform hover:scale-105">
            <i class="fas fa-search mr-1"></i> Cari
          </button>
        </form>

        <!-- Tombol Export Pegawai Tim -->
        <a href="{{ route('admin.pegawai.export', ['tipe' => 'tim']) }}"
          class="inline-flex items-center px-4 py-2 border border-green-500 text-green-600 font-medium 
                 rounded-lg backdrop-blur-sm bg-white/30 hover:bg-green-50 hover:text-green-700 
                 transition duration-200 ease-in-out transform hover:scale-105 shadow-sm">
          <i class="fas fa-file-excel text-lg mr-2"></i>
          Export Pegawai Tim
        </a>
      </div>
    </div>

    <!-- Tabel Pegawai Tim -->
    <div class="overflow-x-auto rounded-xl border border-gray-200">
      <table class="w-full table-auto text-sm text-gray-700">
        <thead class="bg-gradient-to-r from-blue-100 to-blue-200 text-gray-700">
          <tr>
            <th class="px-3 py-2 border w-12 text-center">No.</th>
            <th class="px-4 py-2 border">Nama</th>
            <th class="px-4 py-2 border">NIP</th>
            <th class="px-4 py-2 border">Jabatan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pegawai as $p)
          <tr class="hover:bg-gray-50 transition">
            <td class="px-3 py-2 border text-center">{{ $loop->iteration }}</td>
            <td class="px-4 py-2 border">{{ $p->nama }}</td>
            <td class="px-4 py-2 border">{{ $p->nip }}</td>
            <td class="px-4 py-2 border">{{ $p->jabatan }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="px-4 py-4 border text-center text-gray-500">
              Tidak ada data pegawai yang tersedia.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ================== DAFTAR PEGAWAI GLOBAL ================== --}}
  <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
      <h3 class="text-xl font-semibold text-blue-600">
        Daftar Pegawai Global
      </h3>

      <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
        <!-- Form Search Pegawai Global -->
        <form method="GET" action="{{ route('admin.pegawai.index') }}" class="flex gap-3 w-full sm:w-auto">
          <input type="text" name="search_global" value="{{ request('search_global') }}"
            class="px-4 py-2 w-full sm:w-64 border border-gray-300 rounded-lg 
                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                   bg-white/50 backdrop-blur-sm placeholder-gray-500"
            placeholder="Cari pegawai global...">
          <button type="submit"
            class="px-4 py-2 rounded-lg border border-gray-400 text-gray-600 font-medium 
                   bg-white/40 backdrop-blur-sm hover:bg-gray-100 hover:text-gray-700
                   transition duration-200 ease-in-out transform hover:scale-105">
            <i class="fas fa-search mr-1"></i> Cari
          </button>
        </form>

        <!-- Tombol Export Pegawai Global -->
        <a href="{{ route('admin.pegawai.export') }}"
          class="inline-flex items-center px-4 py-2 border border-green-500 text-green-600 font-medium 
                 rounded-lg backdrop-blur-sm bg-white/30 hover:bg-green-50 hover:text-green-700 
                 transition duration-200 ease-in-out transform hover:scale-105 shadow-sm">
          <i class="fas fa-file-excel text-lg mr-2"></i>
          Export Pegawai Global
        </a>
      </div>
    </div>

    <!-- Tabel Pegawai Global -->
    <div class="overflow-x-auto rounded-xl border border-gray-200">
      <table class="min-w-full table-auto text-sm text-gray-700">
        <thead class="bg-gradient-to-r from-blue-100 to-blue-200 text-gray-700">
          <tr>
            <th class="px-3 py-2 border w-12 text-center whitespace-nowrap">No.</th>
            <th class="px-4 py-2 border">Nama</th>
            <th class="px-4 py-2 border">NIP</th>
            <th class="px-4 py-2 border">Tim</th>
            <th class="px-4 py-2 border">Jabatan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pegawaiGlobal as $pg)
          <tr class="hover:bg-gray-50 transition">
            <td class="px-3 py-2 border text-center">{{ $loop->iteration }}</td>
            <td class="px-4 py-2 border">{{ $pg->nama }}</td>
            <td class="px-4 py-2 border">{{ $pg->nip }}</td>
            <td class="px-4 py-2 border">{{ $pg->team->nama_tim ?? '-' }}</td>
            <td class="px-4 py-2 border">{{ $pg->jabatan }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="px-4 py-4 border text-center text-gray-500">
              Tidak ada pegawai di sistem.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

{{-- Footer --}}
<footer class="text-center text-sm text-gray-500 py-4 border-t mt-10">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>
@endsection