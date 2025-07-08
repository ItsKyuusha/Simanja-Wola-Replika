@extends('layouts.app')
@section('title', 'Data Pegawai')

@section('content')
<h1 class="mb-4">Data Pegawai</h1>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>No.</th>
      <th>Nama</th>
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
@endsection
