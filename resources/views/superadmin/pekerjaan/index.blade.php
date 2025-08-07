@extends('layouts.app')

@section('page-title', 'Pekerjaan')

@section('content')
<div class="bg-white rounded-xl p-6 mb-8 border border-gray-200">

  <h3 class="text-2xl font-semibold text-gray-700 mb-6">Data Progress Pekerjaan</h3>

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
  <h3 class="text-2xl font-semibold text-gray-700 mb-4">Tabel Pekerjaan</h3>

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
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
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
        @forelse($tugas as $tugas)
        <tr class="text-center odd:bg-white even:bg-gray-50 hover:bg-gray-100">
          <td class="px-3 py-2 border">{{ $loop->iteration }}</td>
          <td class="text-left px-3 py-2 border">{{ $tugas->nama_tugas }}</td>
          <td class="px-3 py-2 border text-purple-600">{{ $tugas->jenisPekerjaan->bobot ?? 0 }}</td>
          <td class="text-left px-3 py-2 border">{{ $tugas->asal ?? '-' }}</td>
          <td class="px-3 py-2 border">{{ $tugas->target }}</td>
          <td class="px-3 py-2 border">{{ $tugas->realisasi->realisasi ?? '-' }}</td>
          <td class="px-3 py-2 border">{{ $tugas->satuan }}</td>
          <td class="px-3 py-2 border text-red-500 font-medium">{{ \Carbon\Carbon::parse($tugas->deadline)->format('d M Y') }}</td>
          <td class="px-3 py-2 border">{{ optional($tugas->realisasi)->tanggal_realisasi ?? '-' }}</td>
          <td class="px-3 py-2 border text-green-600">{{ $tugas->realisasi->nilai_kualitas ?? '-' }}</td>
          <td class="px-3 py-2 border text-yellow-600">{{ $tugas->realisasi->nilai_kuantitas ?? '-' }}</td>
          <td class="text-left px-3 py-2 border italic text-gray-600">{{ $tugas->realisasi->catatan ?? '-' }}</td>
          <td class="px-3 py-2 border">
            @if($tugas->realisasi && $tugas->realisasi->file_bukti)
            <a href="{{ asset('storage/' . $tugas->realisasi->file_bukti) }}" target="_blank" class="text-blue-600 hover:underline font-semibold">Lihat</a>
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
    <footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
        © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
    </footer>
@endsection