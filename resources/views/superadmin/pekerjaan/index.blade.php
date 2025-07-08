@extends('layouts.app')
@section('title', 'Data Pekerjaan')

@section('content')
<h3 class="mb-3">Tabel Pekerjaan</h3>

<table class="table table-bordered table-sm">
  <thead class="table-primary text-center">
    <tr>
      <th>No.</th>
      <th>Nama Tugas</th>
      <th>Bobot</th>
      <th>Asal</th>
      <th>Target</th>
      <th>Realisasi</th>
      <th>Satuan</th>
      <th>Deadline</th>
      <th>Tanggal Realisasi</th>
      <th>Nilai Kualitas</th>
      <th>Nilai Kuantitas</th>
      <th>Catatan</th>
      <th>Bukti</th>
    </tr>
  </thead>
  <tbody>
    @php $rowNumber = 1; @endphp
    @forelse($progress as $p)
      @forelse($p->pegawai->tugas as $tugas)
        <tr>
          <td>{{ $rowNumber++ }}</td>
          <td>{{ $tugas->nama_tugas }}</td>
          <td class="text-center">{{ $tugas->jenisPekerjaan->bobot ?? 0 }}</td>
          <td>{{ $tugas->asal ?? '-' }}</td>
          <td class="text-center">{{ $tugas->target }}</td>
          <td class="text-center">{{ $tugas->realisasi->realisasi ?? '-' }}</td>
          <td>{{ $tugas->satuan }}</td>
          <td>{{ \Carbon\Carbon::parse($tugas->deadline)->format('d M Y') }}</td>
          <td>{{ optional($tugas->realisasi)->tanggal_realisasi ?? '-' }}</td>
          <td class="text-center">{{ $tugas->realisasi->nilai_kualitas ?? '-' }}</td>
          <td class="text-center">{{ $tugas->realisasi->nilai_kuantitas ?? '-' }}</td>
          <td class="text-center">{{ $tugas->realisasi->catatan ?? '-' }}</td>
          <td class="text-center">
            @if($tugas->realisasi && $tugas->realisasi->file_bukti)
              <a href="{{ asset('storage/' . $tugas->realisasi->file_bukti) }}" target="_blank">Lihat</a>
            @else
              -
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="13" class="text-center">Tidak ada tugas untuk {{ $p->pegawai->nama }}</td>
        </tr>
      @endforelse
    @empty
      <tr>
        <td colspan="13" class="text-center">Tidak ada data pekerjaan yang tersedia.</td>
      </tr>
    @endforelse
  </tbody>
</table>
@endsection
