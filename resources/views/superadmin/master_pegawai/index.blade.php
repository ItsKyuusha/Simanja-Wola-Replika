@extends('layouts.app')
@section('title', 'Manajemen Pegawai')

@section('content')
<div class="card mb-4 shadow-sm">
  <div class="card-body">
    <h3 class="mb-3">Tabel Pegawai</h3>

    <!-- Form Pencarian -->
    <div class="row mb-3 justify-content-end">
      <div class="col-md-12 col-lg-12 d-flex justify-content-end">
        <form method="GET" action="{{ route('superadmin.master_pegawai.index') }}" class="d-flex me-2 flex-grow-1">
          <input type="text" name="search" class="form-control" placeholder="Cari nama pegawai ..." value="{{ request('search') }}">
          <button type="submit" class="btn btn-outline-secondary ms-2">Cari</button>
        </form>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Tabel Pegawai -->
    <table class="table table-bordered table-sm">
      <thead class="table-primary text-center">
        <tr>
          <th>No.</th>
          <th>Nama Pegawai</th>
          <th>NIP</th>
          <th>Jabatan</th>
          <th>Tim</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $pegawai)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $pegawai->nama }}</td>
          <td>{{ $pegawai->nip }}</td>
          <td>{{ $pegawai->jabatan }}</td>
          <td>{{ $pegawai->team->nama_tim ?? '-' }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center">Tidak ada data pegawai yang tersedia.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
