@extends('layouts.app')
@section('page-title', 'Progress')

@section('content')
<div class="bg-white rounded-2xl p-6 mb-12 border border-gray-200">

  {{-- Judul dan Form Search --}}
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 flex-wrap">
    <h2 class="text-2xl font-semibold text-blue-600">Rekap Tugas Tim</h2>
    <div class="flex flex-col sm:flex-row items-center gap-3">
      <form method="GET" action="{{ route('admin.progress.index') }}" class="flex gap-3 w-full sm:w-auto">
        <input type="text" name="search" value="{{ request('search') }}"
          class="px-4 py-2 w-full sm:w-64 border border-gray-300 rounded-lg 
             focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400
             bg-white/50 backdrop-blur-sm placeholder-gray-500"
          placeholder="Cari nama pegawai atau tugas...">
        <button type="submit"
          class="px-4 py-2 rounded-lg border border-gray-400 text-gray-600 font-medium 
             bg-white/40 backdrop-blur-sm hover:bg-gray-100 hover:text-gray-700
             transition duration-200 ease-in-out transform hover:scale-105">
          <i class="fas fa-search mr-1"></i> Cari
        </button>
      </form>
      <!-- Tombol Export -->
      <a href="{{ route('admin.progress.export') }}"
        class="inline-flex items-center px-4 py-2 rounded-lg border border-green-400 text-green-600 font-medium
           bg-green-200/20 backdrop-blur-sm shadow-sm 
           hover:bg-green-300/30 hover:border-green-500 hover:text-green-700
           transition duration-200 ease-in-out transform hover:scale-105">
        <i class="fas fa-file-excel mr-2"></i> Export Tabel
      </a>
    </div>
  </div>

  {{-- Table --}}
  <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
    <table class="w-full table-auto text-sm text-gray-700">
      <thead class="bg-gradient-to-r from-blue-100 to-blue-200 text-center text-sm text-gray-700">
        <tr>
          <th class="px-3 py-2 border">No.</th>
          <th class="px-3 py-2 border">Nama Pegawai</th>
          <th class="px-3 py-2 border">Nama Tugas</th>
          <th class="px-3 py-2 border">Target</th>
          <th class="px-3 py-2 border">Realisasi</th>
          <th class="px-3 py-2 border">Kualitas</th>
          <th class="px-3 py-2 border">Kuantitas</th>
          <th class="px-3 py-2 border">Status</th>
          <th class="px-3 py-2 border">Approve</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tugas as $t)
        @php
        $realisasi = $t->realisasi;
        $isApproved = $realisasi?->is_approved ?? false;
        @endphp
        <tr class="text-center hover:bg-gray-50">
          <td class="px-3 py-2 border">{{ $loop->iteration }}</td>
          <td class="px-3 py-2 border">{{ $t->pegawai->nama ?? '-' }}</td>
          <td class="px-3 py-2 border">{{ $t->nama_tugas }}</td>
          <td class="px-3 py-2 border">{{ $t->target }} {{ $t->satuan }}</td>
          <td class="px-3 py-2 border">
            {{ $realisasi->realisasi ?? '-' }}
          </td>
          <td class="px-3 py-2 border">{{ $realisasi->nilai_kualitas ?? '-' }}</td>
          <td class="px-3 py-2 border">{{ $realisasi->nilai_kuantitas ?? '-' }}</td>
          <td class="px-3 py-2 border">
            @if (!$realisasi)
            <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded">
              Belum Dikerjakan
            </span>
            @elseif (!$isApproved)
            <span class="inline-block px-2 py-1 text-xs font-semibold text-black bg-yellow-300 rounded">
              Menunggu Persetujuan
            </span>
            @elseif ($realisasi->realisasi < $t->target)
              <span class="inline-block px-2 py-1 text-xs font-semibold text-black bg-yellow-300 rounded">
                Ongoing
              </span>
              @else
              <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-green-600 rounded">
                Selesai Dikerjakan
              </span>
              @endif
          </td>
          <td class="px-3 py-2 border">
            @if ($realisasi && !$isApproved && auth()->user()->pegawai && $t->asal === auth()->user()->pegawai->nama)
            <form method="POST" action="{{ route('admin.progress.approve', $realisasi->id) }}">
              @csrf
              @method('PATCH')
              <button type="submit"
                class="flex items-center px-3 py-1.5 text-xs font-semibold rounded border border-blue-400 text-blue-600
           bg-white/50 hover:bg-blue-600 hover:text-white hover:border-blue-600
           transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M5 13l4 4L19 7" />
                </svg>
                Approve
              </button>
            </form>
            @else
            -
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" class="text-center px-3 py-4 border text-gray-500">Tidak ada data tugas.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>
@endsection