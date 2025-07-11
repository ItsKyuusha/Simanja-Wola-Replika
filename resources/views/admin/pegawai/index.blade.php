@extends('layouts.app')
@section('title', 'Anggota Tim')

@section('content')
<div class="card shadow-sm">
  <div class="card-body">
<h3>Daftar Pegawai dalam Tim Anda</h3>
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
<hr>

<div class="card shadow-sm">
  <div class="card-body">
<h3>Daftar Pegawai Global</h3>

<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th>No.</th>
      <th>Nama</th>
      <th>NIP</th>
      <th>Tim</th>
      <th>Jabatan</th>
    </tr>
  </thead>
  <tbody>
    @forelse($pegawaiGlobal as $pg)
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td>{{ $pg->nama }}</td>
      <td>{{ $pg->nip }}</td>
      <td>{{ $pg->team->nama_tim ?? '-' }}</td>
      <td>{{ $pg->jabatan }}</td>
    </tr>
    @empty
    <tr>
      <td colspan="5" class="text-center">Tidak ada pegawai di sistem.</td>
    </tr>
    @endforelse
  </tbody>
</table>
</div>
</div>
@endsection
