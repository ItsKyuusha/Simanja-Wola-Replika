@extends('layouts.app')
@section('page-title', 'Pegawai')

@section('content')
<div class="bg-white shadow rounded-md p-6 mb-6">
  <h3 class="text-xl font-semibold text-gray-700 mb-4">Daftar Pegawai dalam Tim Anda</h3>
  <div class="overflow-x-auto">
    <table class="min-w-full table-auto border border-gray-300 text-sm text-left">
      <thead class="bg-blue-100 text-gray-700">
        <tr>
          <th class="px-2 py-2 border w-12 text-center">No.</th>
          <th class="px-4 py-2 border">Nama</th>
          <th class="px-4 py-2 border">NIP</th>
          <th class="px-4 py-2 border">Jabatan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pegawai as $p)
        <tr class="hover:bg-gray-50">
          <td class="px-2 py-2 border text-center w-12">{{ $loop->iteration }}</td>
          <td class="px-4 py-2 border">{{ $p->nama }}</td>
          <td class="px-4 py-2 border">{{ $p->nip }}</td>
          <td class="px-4 py-2 border">{{ $p->jabatan }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="px-4 py-4 border text-center text-gray-500">Tidak ada data pegawai yang tersedia.</td>
        </tr>
        @endforelse
      </tbody>

    </table>
  </div>
</div>

<div class="bg-white shadow rounded-md p-6">
  <h3 class="text-xl font-semibold text-gray-700 mb-4">Daftar Pegawai Global</h3>
  <div class="overflow-x-auto">
    <table class="min-w-full table-auto border border-gray-300 text-sm text-left">
      <thead class="bg-blue-100 text-gray-700">
        <tr>
          <th class="px-2 py-2 border w-12 text-center whitespace-nowrap">No.</th>
          <th class="px-4 py-2 border">Nama</th>
          <th class="px-4 py-2 border">NIP</th>
          <th class="px-4 py-2 border">Tim</th>
          <th class="px-4 py-2 border">Jabatan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pegawaiGlobal as $pg)
        <tr class="hover:bg-gray-50">
          <td class="px-2 py-2 border w-12 text-center whitespace-nowrap">{{ $loop->iteration }}</td>
          <td class="px-4 py-2 border">{{ $pg->nama }}</td>
          <td class="px-4 py-2 border">{{ $pg->nip }}</td>
          <td class="px-4 py-2 border">{{ $pg->team->nama_tim ?? '-' }}</td>
          <td class="px-4 py-2 border">{{ $pg->jabatan }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="px-4 py-4 border text-center text-gray-500">Tidak ada pegawai di sistem.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>
@endsection