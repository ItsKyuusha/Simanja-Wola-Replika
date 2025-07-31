@extends('layouts.app')

@section('page-title', 'Progress')


@section('content')

<!-- CARD: Tabel Kinerja Pegawai -->
<div class="bg-white rounded-2xl shadow p-6 mb-12 border border-gray-200">
  <h3 class="text-2xl font-semibold text-gray-700 mb-4">📒 Tabel Kinerja Pegawai</h3>

  <div class="overflow-x-auto rounded-md border border-gray-200">
    <table class="table-auto w-full text-sm border border-gray-200">
      <thead class="bg-blue-100 text-center text-sm text-gray-700">
        <tr class="text-center text-xs uppercase tracking-wide">
          <th class="px-4 py-3 border">No.</th>
          <th class="px-4 py-3 border">Nama Pegawai</th>
          <th class="px-4 py-3 border">Nama Tugas</th>
          <th class="px-4 py-3 border">Bobot</th>
          <th class="px-4 py-3 border">Asal</th>
          <th class="px-4 py-3 border">Target</th>
          <th class="px-4 py-3 border">Realisasi</th>
          <th class="px-4 py-3 border">Satuan</th>
          <th class="px-4 py-3 border">Deadline</th>
          <th class="px-4 py-3 border">Tgl Realisasi</th>
          <th class="px-4 py-3 border">Nilai Kualitas</th>
          <th class="px-4 py-3 border">Nilai Kuantitas</th>
          <th class="px-4 py-3 border">Catatan</th>
          <th class="px-4 py-3 border">Bukti</th>
        </tr>
      </thead>
      <tbody>
        @forelse($progress as $p)
        @foreach($p->pegawai->tugas as $tugas)
        <tr class="text-center odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition duration-200">
          <td class="px-3 py-2 border">{{ $loop->parent->iteration }}</td>
          <td class="text-left px-3 py-2 border font-medium">{{ $p->pegawai->nama }}</td>
          <td class="text-left px-3 py-2 border">{{ $tugas->nama_tugas }}</td>
          <td class="px-3 py-2 border">{{ $tugas->jenisPekerjaan->bobot ?? 0 }}</td>
          <td class="text-left px-3 py-2 border">{{ $tugas->asal ?? '-' }}</td>
          <td class="px-3 py-2 border">{{ $tugas->target }}</td>
          <td class="px-3 py-2 border">{{ $tugas->realisasi->realisasi ?? '-' }}</td>
          <td class="px-3 py-2 border">{{ $tugas->satuan }}</td>
          <td class="px-3 py-2 border text-red-500">{{ \Carbon\Carbon::parse($tugas->deadline)->format('d M Y') }}</td>
          <td class="px-3 py-2 border">{{ optional($tugas->realisasi)->tanggal_realisasi ?? '-' }}</td>
          <td class="px-3 py-2 border text-green-600">{{ $tugas->realisasi->nilai_kualitas ?? '-' }}</td>
          <td class="px-3 py-2 border text-blue-600">{{ $tugas->realisasi->nilai_kuantitas ?? '-' }}</td>
          <td class="text-left px-3 py-2 border text-gray-500 italic">{{ $tugas->realisasi->catatan ?? '-' }}</td>
          <td class="px-3 py-2 border">
            @if($tugas->realisasi && $tugas->realisasi->file_bukti)
            <a href="{{ asset('storage/' . $tugas->realisasi->file_bukti) }}" target="_blank"
              class="text-blue-600 hover:underline">Lihat</a>
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
<div class="bg-white rounded-2xl shadow p-6 mb-16 border border-gray-200">
  <h3 class="text-2xl font-semibold text-gray-700 mb-4">🎯 Tabel Nilai Akhir Pegawai</h3>

  <div class="overflow-x-auto rounded-md border border-gray-200">
    <table class="table-auto w-full text-sm border border-gray-200">
      <thead class="bg-blue-100 text-center text-sm text-gray-700">
        <tr class="text-center text-xs uppercase tracking-wide">
          <th class="px-4 py-3 border">No.</th>
          <th class="px-4 py-3 border">Nama Pegawai</th>
          <th class="px-4 py-3 border">NIP</th>
          <th class="px-4 py-3 border">Total Bobot</th>
          <th class="px-4 py-3 border">Nilai Akhir</th>
        </tr>
      </thead>
      <tbody>
        @forelse($progress as $p)
        <tr class="text-center odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition duration-200">
          <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
          <td class="text-left px-4 py-2 border font-medium">{{ $p->pegawai->nama }}</td>
          <td class="px-4 py-2 border">{{ $p->pegawai->nip }}</td>
          <td class="px-4 py-2 border">{{ $p->total_bobot }}</td>
          <td class="px-4 py-2 border text-blue-700 font-semibold">{{ $p->nilai_akhir }}</td>
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