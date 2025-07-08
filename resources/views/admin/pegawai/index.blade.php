@extends('layouts.app')
@section('title', 'Anggota Tim')

@section('content')
<h4>Daftar Pegawai dalam Tim Anda</h4>

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
@endsection
