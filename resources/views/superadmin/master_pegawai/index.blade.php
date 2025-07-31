@extends('layouts.app')

@section('page-title', 'Master | Pegawai')


@section('content')
<div class="bg-white rounded-2xl shadow p-6 mb-12 border border-gray-200">
  <h3 class="text-2xl font-semibold text-gray-700 mb-4">📋 Tabel Pegawai</h3>

  <!-- Form Pencarian -->
  <div class="flex justify-end mb-4">
    <form method="GET" action="{{ route('superadmin.master_pegawai.index') }}" class="flex w-full max-w-md gap-2">
      <input type="text" name="search" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-400" placeholder="Cari nama pegawai ..." value="{{ request('search') }}">
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Cari</button>
    </form>
  </div>

  <!-- Alert Sukses -->
  @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md">
      {{ session('success') }}
    </div>
  @endif

  <!-- Tabel Pegawai -->
  <div class="overflow-x-auto rounded-md border border-gray-200">
    <table class="min-w-full text-sm text-gray-800 border-collapse">
      <thead class="bg-blue-100 text-gray-700 font-semibold">
        <tr class="text-center text-xs uppercase tracking-wide">
          <th class="px-4 py-3 border">No.</th>
          <th class="px-4 py-3 border">Nama Pegawai</th>
          <th class="px-4 py-3 border">NIP</th>
          <th class="px-4 py-3 border">Jabatan</th>
          <th class="px-4 py-3 border">Tim</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $pegawai)
        <tr class="text-center odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition duration-200">
          <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
          <td class="text-left px-4 py-2 border font-medium">{{ $pegawai->nama }}</td>
          <td class="px-4 py-2 border">{{ $pegawai->nip }}</td>
          <td class="text-left px-4 py-2 border">{{ $pegawai->jabatan }}</td>
          <td class="text-left px-4 py-2 border">{{ $pegawai->team->nama_tim ?? '-' }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center px-4 py-6 text-gray-500 italic">Tidak ada data pegawai yang tersedia.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
