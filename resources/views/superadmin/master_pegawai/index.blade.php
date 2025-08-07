@extends('layouts.app')

@section('page-title', 'Master | Pegawai')


@section('content')
<div class="bg-white rounded-2xl shadow p-6 mb-12 border border-gray-200">
 <!-- Judul dan Form Pencarian sejajar -->
<div class="flex justify-between items-center mb-4 flex-wrap gap-4">
  <h3 class="text-2xl font-semibold text-gray-700">Tabel Pegawai</h3>

  <!-- Form Pencarian -->
  <form method="GET" action="{{ route('superadmin.master_pegawai.index') }}" class="flex gap-2 w-full sm:w-auto">
    <input type="text" name="search"
      class="w-full sm:w-72 border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200"
      placeholder="Cari nama pegawai ..." value="{{ request('search') }}">
    <button type="submit"
       class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md text-gray-700 border border-gray-300">
      Cari
    </button>
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
      <thead class="bg-blue-100 text-center text-sm text-gray-700">
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
 <!-- Footer -->
    <footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
        © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
    </footer>
@endsection
