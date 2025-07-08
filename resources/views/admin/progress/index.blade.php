@extends('layouts.app')
@section('title', 'Progress Tugas')

@section('content')
<h4>Rekap Tugas Tim</h4>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>No.</th>
      <th>Nama Pegawai</th>
      <th>Nama Tugas</th>
      <th>Target</th>
      <th>Realisasi</th>
      <th>Kualitas</th>
      <th>Kuantitas</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    @forelse($tugas as $t)
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td>{{ $t->pegawai->nama }}</td>
      <td>{{ $t->nama_tugas }}</td>
      <td>{{ $t->target }} {{ $t->satuan }}</td>
      <td>{{ $t->realisasi->realisasi ?? '-' }}</td>
      <td>{{ $t->realisasi->nilai_kualitas ?? '-' }}</td>
      <td>{{ $t->realisasi->nilai_kuantitas ?? '-' }}</td>
      <td>
          @if (!$t->realisasi)
            Belum Dikerjakan
          @elseif ($t->realisasi->realisasi < $t->target)
            Ongoing
          @else
            Selesai Dikerjakan
          @endif
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="8" class="text-center">Tidak ada data tugas.</td>
    </tr>
    @endforelse
  </tbody>
</table>
@endsection
