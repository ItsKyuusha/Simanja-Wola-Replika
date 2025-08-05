@extends('layouts.app')

@section('page-title', 'Progress')

@section('content')

<!-- CARD: Tabel Kinerja Pegawai -->
<div class="bg-white rounded-2xl p-6 mb-12 border border-gray-200">
  <h3 class="text-2xl font-semibold text-gray-700 mb-4">📒 Tabel Kinerja Pegawai</h3>

  <div class="overflow-x-auto rounded-md border border-gray-200">
    <table class="min-w-full text-sm text-gray-800">
      <thead class="bg-blue-600 text-white">
        <tr class="text-xs uppercase tracking-wide text-center">
          <th class="px-4 py-3">No.</th>
          <th class="px-4 py-3 text-left">Nama Pegawai</th>
          <th class="px-4 py-3 text-left">Nama Tugas</th>
          <th class="px-4 py-3">Bobot</th>
          <th class="px-4 py-3 text-left">Asal</th>
          <th class="px-4 py-3">Target</th>
          <th class="px-4 py-3">Realisasi</th>
          <th class="px-4 py-3">Satuan</th>
          <th class="px-4 py-3 text-red-100">Deadline</th>
          <th class="px-4 py-3">Tgl Realisasi</th>
          <th class="px-4 py-3">Nilai Kualitas</th>
          <th class="px-4 py-3">Nilai Kuantitas</th>
          <th class="px-4 py-3 text-left">Catatan</th>
          <th class="px-4 py-3">Bukti</th>
        </tr>
      </thead>
      <tbody>
        @forelse($progress as $p)
        @foreach($p->pegawai->tugas as $tugas)
        <tr class="text-center odd:bg-white even:bg-gray-50 hover:bg-blue-50 border-b border-gray-200 transition-colors">
          <td class="px-3 py-2">{{ $loop->parent->iteration }}</td>
          <td class="text-left px-3 py-2 font-medium">{{ $p->pegawai->nama }}</td>
          <td class="text-left px-3 py-2">{{ $tugas->nama_tugas }}</td>
          <td class="px-3 py-2">{{ $tugas->jenisPekerjaan->bobot ?? 0 }}</td>
          <td class="text-left px-3 py-2">{{ $tugas->asal ?? '-' }}</td>
          <td class="px-3 py-2">{{ $tugas->target }}</td>
          <td class="px-3 py-2">{{ $tugas->realisasi->realisasi ?? '-' }}</td>
          <td class="px-3 py-2">{{ $tugas->satuan }}</td>
          <td class="px-3 py-2 text-red-600">{{ \Carbon\Carbon::parse($tugas->deadline)->format('d M Y') }}</td>
          <td class="px-3 py-2">{{ optional($tugas->realisasi)->tanggal_realisasi ?? '-' }}</td>
          <td class="px-3 py-2 text-green-600">{{ $tugas->realisasi->nilai_kualitas ?? '-' }}</td>
          <td class="px-3 py-2 text-blue-600">{{ $tugas->realisasi->nilai_kuantitas ?? '-' }}</td>
          <td class="text-left px-3 py-2 text-gray-500 italic">{{ $tugas->realisasi->catatan ?? '-' }}</td>
          <td class="px-3 py-2">
            @if($tugas->realisasi && $tugas->realisasi->file_bukti)
            <a href="{{ asset('storage/' . $tugas->realisasi->file_bukti) }}" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
            @else
            <span class="text-gray-400">-</span>
            @endif
          </td>
        </tr>
        @endforeach
        @empty
        <tr>
          <td colspan="14" class="text-center py-6 text-gray-500">Tidak ada data kinerja pegawai.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- CARD: Tabel Nilai Akhir Pegawai -->
<div class="bg-white rounded-2xl p-6 mb-16 border border-gray-200">
  <h3 class="text-2xl font-semibold text-gray-700 mb-4">🎯 Tabel Nilai Akhir Pegawai</h3>

  <div class="overflow-x-auto rounded-md border border-gray-200">
    <table class="min-w-full text-sm text-gray-800">
      <thead class="bg-blue-600 text-white">
        <tr class="text-xs uppercase tracking-wide text-center">
          <th class="px-4 py-3">No.</th>
          <th class="px-4 py-3 text-left">Nama Pegawai</th>
          <th class="px-4 py-3">NIP</th>
          <th class="px-4 py-3">Total Bobot</th>
          <th class="px-4 py-3">Nilai Akhir</th>
        </tr>
      </thead>
      <tbody>
        @forelse($progress as $p)
        <tr class="text-center odd:bg-white even:bg-gray-50 hover:bg-blue-50 border-b border-gray-200 transition-colors">
          <td class="px-4 py-2">{{ $loop->iteration }}</td>
          <td class="text-left px-4 py-2 font-medium">{{ $p->pegawai->nama }}</td>
          <td class="px-4 py-2">{{ $p->pegawai->nip }}</td>
          <td class="px-4 py-2">{{ $p->total_bobot }}</td>
          <td class="px-4 py-2 text-blue-700 font-semibold">{{ $p->nilai_akhir }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-6 text-gray-500">Tidak ada data nilai akhir pegawai.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Footer -->
<footer class="bg-gray-50 text-center py-4 text-sm text-gray-500 border-t">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>

@endsection
