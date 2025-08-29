@extends('layouts.app')

@section('page-title', 'Pekerjaan')

@section('content')
<div class="bg-white rounded-2xl p-6 mb-12 border border-gray-200">

  <h2 class="text-2xl font-semibold text-blue-600">Data Progres Pekerjaan</h2>

  <!-- Progress Bar -->
  <div class="w-full bg-gray-200 rounded-full h-5 mb-4 overflow-hidden">
    <div
      class="bg-green-500 h-5 text-white text-xs sm:text-sm flex items-center justify-center transition-all duration-300"
      style="width: {{ $persentaseSelesai }}%;">
      {{ $persentaseSelesai }}%
    </div>
  </div>

  <!-- Statistik -->
  <div class="grid grid-cols-2 sm:grid-cols-5 text-center text-gray-600 text-sm mb-6 gap-y-4">
    <div>
      <div class="font-semibold">Persentase</div>
      <div>{{ $persentaseSelesai }}%</div>
    </div>
    <div>
      <div class="font-semibold">Total Tugas</div>
      <div>{{ $totalTugas }}</div>
    </div>
    <div>
      <div class="font-semibold">Selesai</div>
      <div>{{ $tugasSelesai }}</div>
    </div>
    <div>
      <div class="font-semibold">Ongoing</div>
      <div>{{ $tugasOngoing }}</div>
    </div>
    <div>
      <div class="font-semibold">Belum</div>
      <div>{{ $tugasBelum }}</div>
    </div>
  </div>
</div>

<!-- Tabel Pekerjaan -->
<div class="bg-white rounded-xl p-6 border border-gray-200">
  <h2 class="text-2xl font-semibold text-blue-600">Tabel Pekerjaan</h2>

  <!-- Filter Form -->
  <form method="GET" action="{{ route('superadmin.pekerjaan.index') }}" class="grid grid-cols-1 sm:grid-cols-6 gap-3 text-sm mb-6">
    <input type="text" name="search" class="border border-gray-300 rounded-md px-3 py-2" placeholder="Cari Nama Tugas" value="{{ request('search') }}">
    <select name="deadline_month" class="border border-gray-300 rounded-md px-3 py-2">
      <option value="">Bulan Deadline</option>
      @for($i = 1; $i <= 12; $i++)
        <option value="{{ $i }}" {{ request('deadline_month') == $i ? 'selected' : '' }}>
        {{ \Carbon\Carbon::create()->month($i)->format('F') }}
        </option>
        @endfor
    </select>
    <select name="deadline_year" class="border border-gray-300 rounded-md px-3 py-2">
      <option value="">Tahun Deadline</option>
      @for($i = 2020; $i <= now()->year; $i++)
        <option value="{{ $i }}" {{ request('deadline_year') == $i ? 'selected' : '' }}>{{ $i }}</option>
        @endfor
    </select>
    <select name="realisasi_month" class="border border-gray-300 rounded-md px-3 py-2">
      <option value="">Bulan Realisasi</option>
      @for($i = 1; $i <= 12; $i++)
        <option value="{{ $i }}" {{ request('realisasi_month') == $i ? 'selected' : '' }}>
        {{ \Carbon\Carbon::create()->month($i)->format('F') }}
        </option>
        @endfor
    </select>
    <select name="realisasi_year" class="border border-gray-300 rounded-md px-3 py-2">
      <option value="">Tahun Realisasi</option>
      @for($i = 2020; $i <= now()->year; $i++)
        <option value="{{ $i }}" {{ request('realisasi_year') == $i ? 'selected' : '' }}>{{ $i }}</option>
        @endfor
    </select>
    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg border border-blue-500 text-blue-600 font-medium
           bg-blue-200/20 backdrop-blur-sm shadow-sm 
           hover:bg-blue-300/30 hover:border-blue-600 hover:text-blue-700
           transition duration-200 ease-in-out transform hover:scale-105">
      Filter
    </button>
  </form>

  <!-- Tabel -->
  <div class="overflow-x-auto rounded-md border border-gray-200">
    <table class="min-w-full text-sm text-gray-700 table-auto">
      <thead class="bg-blue-100 text-center text-sm text-gray-700">
        <tr>
          <th class="px-3 py-3 border">No.</th>
          <th class="px-3 py-3 border text-left">Nama Tugas</th>
          <th class="px-3 py-3 border">Bobot</th>
          <th class="px-3 py-3 border text-left">Asal</th>
          <th class="px-3 py-3 border">Target</th>
          <th class="px-3 py-3 border">Realisasi</th>
          <th class="px-3 py-3 border">Satuan</th>
          <th class="px-3 py-3 border">Deadline</th>
          <th class="px-3 py-3 border">Tgl Realisasi</th>
          <th class="px-3 py-3 border">Kualitas</th>
          <th class="px-3 py-3 border">Kuantitas</th>
          <th class="px-3 py-3 border text-left">Catatan</th>
          <th class="px-3 py-3 border">Bukti</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tugas as $item)
        <tr class="text-center odd:bg-white even:bg-gray-50 hover:bg-gray-100">
          <td class="px-3 py-2 border">{{ $loop->iteration }}</td>
          <td class="text-left px-3 py-2 border">{{ $item->nama_tugas }}</td>
          <td class="px-3 py-2 border text-purple-600">{{ $item->jenisPekerjaan->bobot ?? 0 }}</td>
          <td class="text-left px-3 py-2 border">{{ $item->asal ?? '-' }}</td>
          <td class="px-3 py-2 border">{{ $item->target }}</td>
          <td class="px-3 py-2 border">{{ $item->realisasi->realisasi ?? '-' }}</td>
          <td class="px-3 py-2 border">{{ $item->satuan }}</td>
          <td class="px-3 py-2 border text-red-500 font-medium">{{ \Carbon\Carbon::parse($item->deadline)->format('d M Y') }}</td>
          <td class="px-3 py-2 border">{{ optional($item->realisasi)->tanggal_realisasi ?? '-' }}</td>
          <td class="px-3 py-2 border text-green-600">{{ $item->realisasi->nilai_kualitas ?? '-' }}</td>
          <td class="px-3 py-2 border text-yellow-600">{{ $item->realisasi->nilai_kuantitas ?? '-' }}</td>
          <td class="text-left px-3 py-2 border italic text-gray-600">{{ $item->realisasi->catatan ?? '-' }}</td>
          <td class="px-3 py-2 border">
            @if($item->realisasi && $item->realisasi->file_bukti)
            <a href="{{ asset('storage/' . $item->realisasi->file_bukti) }}" target="_blank" class="text-blue-600 hover:underline font-semibold">Lihat</a>
            @else
            <span class="text-gray-400">-</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="13" class="text-center py-5 text-gray-500">Tidak ada data pekerjaan yang tersedia.</td>
        </tr>
        @endforelse

      </tbody>
    </table>
  </div>
</div>

<!-- Footer -->
@if ($tugas->hasPages())
<div class="flex items-center justify-between border-t border-white/10 px-4 py-3 sm:px-6">
  <!-- Mobile Previous/Next -->
  <div class="flex flex-1 justify-between sm:hidden">
    @if ($tugas->onFirstPage())
    <span class="relative inline-flex items-center rounded-md border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">Previous</span>
    @else
    <a href="{{ $tugas->previousPageUrl() }}" class="relative inline-flex items-center rounded-md border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-white/10">Previous</a>
    @endif

    @if ($tugas->hasMorePages())
    <a href="{{ $tugas->nextPageUrl() }}" class="relative ml-3 inline-flex items-center rounded-md border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-white/10">Next</a>
    @else
    <span class="relative ml-3 inline-flex items-center rounded-md border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">Next</span>
    @endif
  </div>

  <!-- Desktop -->
  <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
    <div>
      <p class="text-sm text-black">
        Menampilkan
        <span class="font-medium">{{ $tugas->firstItem() }}</span>
        sampai
        <span class="font-medium">{{ $tugas->lastItem() }}</span>
        data dari
        <span class="font-medium">{{ $tugas->total() }}</span>
        data keseluruhan
      </p>
    </div>
    <div>
      <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md">
        {{-- Tombol Previous --}}
        @if ($tugas->onFirstPage())
        <span class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 cursor-not-allowed">
          <span class="sr-only">Previous</span>
          <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
            <path d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" />
          </svg>
        </span>
        @else
        <a href="{{ $tugas->previousPageUrl() }}" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 hover:bg-white/5">
          <span class="sr-only">Previous</span>
          <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
            <path d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" />
          </svg>
        </a>
        @endif

        {{-- Nomor Halaman --}}
        @foreach ($tugas->getUrlRange(1, $tugas->lastPage()) as $page => $url)
        @if ($page == $tugas->currentPage())
        <span aria-current="page" class="relative z-10 inline-flex items-center bg-blue-700 px-4 py-2 text-sm font-semibold text-white">{{ $page }}</span>
        @else
        <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-400 hover:bg-white/5">{{ $page }}</a>
        @endif
        @endforeach

        {{-- Tombol Next --}}
        @if ($tugas->hasMorePages())
        <a href="{{ $tugas->nextPageUrl() }}" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 hover:bg-white/5">
          <span class="sr-only">Next</span>
          <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
            <path d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" />
          </svg>
        </a>
        @else
        <span class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 cursor-not-allowed">
          <span class="sr-only">Next</span>
          <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
            <path d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" />
          </svg>
        </span>
        @endif
      </nav>
    </div>
  </div>
</div>
@endif

<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>
@endsection