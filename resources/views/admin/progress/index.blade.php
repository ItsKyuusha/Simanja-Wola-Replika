@extends('layouts.app')
@section('title', 'Progress Tugas')

@section('content')
<div class="card shadow-sm">
  <div class="card-body">
<h3 class="mb-4">Rekap Tugas Tim</h3>
{{-- Form Search --}}
<form action="{{ route('admin.progress.index') }}" method="GET" class="mb-3">
  <div class="input-group">
    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama pegawai atau tugas..." value="{{ request('search') }}">
    <button class="btn btn-outline-secondary" type="submit">Cari</button>
  </div>
</form>

<table class="table table-bordered table-sm">
  <thead class="table-primary text-center">
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
            <span class="badge bg-secondary">Belum Dikerjakan</span>
        @elseif ($t->realisasi->realisasi < $t->target)
            <span class="badge bg-warning text-dark">Ongoing</span>
        @else
            <span class="badge bg-success">Selesai Dikerjakan</span>
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
</div>
</div>
@endsection
