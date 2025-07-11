@extends('layouts.app')
@section('title', 'Anggota Tim')

@section('content')
<div class="card shadow-sm">
  <div class="card-body">
<h4>Daftar Anggota Tim Anda</h4>

<!-- Form Pencarian dalam satu baris -->
<form method="GET" action="{{ route('user.pegawai.index') }}" class="row mb-4">
  <div class="col-md-10">
    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari Nama atau NIP Anggota Tim...">
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-primary w-100">Cari</button>
  </div>
</form>

<!-- Tabel Anggota Tim -->
<table class="table table-bordered">
  <thead>
    <tr>
      <th>No.</th>
      <th>Nama</th>
      <th>NIP</th>
      <th>Jabatan</th>
    </tr>
  </thead>
  <tbody>
    @forelse($pegawai as $p)
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td>{{ $p->nama }}</td>
      <td>{{ $p->nip }}</td>
      <td>{{ $p->jabatan }}</td>
    </tr>
    @empty
    <tr>
      <td colspan="4" class="text-center">Tidak ada data pegawai yang tersedia.</td>
    </tr>
    @endforelse
  </tbody>
</table>
</div>
</div>
@endsection
