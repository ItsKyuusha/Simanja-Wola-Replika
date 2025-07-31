@extends('layouts.app')

@section('page-title', 'Master | Jenis TIM')


@section('content')
<div class="bg-white rounded-2xl shadow p-6 mb-12 border border-gray-200" x-data="{ openCreate: false, openEdit: null }">
  <h3 class="text-2xl font-semibold text-gray-700 mb-4">📋 Tabel Jenis Tim</h3>

  <!-- Form Pencarian & Tambah -->
  <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-3">
    <form action="{{ route('superadmin.jenis-tim.index') }}" method="GET" class="flex w-full md:w-auto gap-2">
      <input type="text" name="search" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Cari jenis tim..." value="{{ request('search') }}">
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Cari</button>
    </form>

    <button @click="openCreate = true" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
      Tambah Jenis Tim
    </button>
  </div>

  @if(session('success'))
  <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md">
    {{ session('success') }}
  </div>
  @endif

  <div class="overflow-x-auto border rounded-md">
    <table class="min-w-full text-sm text-gray-800 border-collapse">
      <thead class="bg-blue-100 text-gray-700 font-semibold">
        <tr class="text-center text-xs uppercase tracking-wide">
          <th class="px-4 py-3 border w-12">No</th>
          <th class="px-4 py-3 border text-left">Jenis Tim</th>
          <th class="px-4 py-3 border w-40">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $tim)
        <tr class="text-center odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition duration-200">
          <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
          <td class="text-left px-4 py-2 border">{{ $tim->nama_tim }}</td>
          <td class="px-4 py-2 border flex justify-center items-center gap-2">
            <button @click="openEdit = {{ $tim->id }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-3 py-1 rounded">Edit</button>
            <form action="{{ route('superadmin.jenis-tim.destroy', $tim->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1 rounded">Hapus</button>
            </form>
          </td>
        </tr>

        <!-- Modal Edit -->
        <template x-if="openEdit === {{ $tim->id }}">
          <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
              <button @click="openEdit = null" class="absolute top-2 right-3 text-gray-600 hover:text-red-500 text-2xl">&times;</button>
              <h2 class="text-lg font-semibold mb-4">Edit Jenis Tim</h2>
              <form action="{{ route('superadmin.jenis-tim.update', $tim->id) }}" method="POST">
                @csrf @method('PUT')
                <input type="text" name="nama_tim" value="{{ $tim->nama_tim }}" required
                  class="w-full px-4 py-2 mb-4 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                <div class="flex justify-end gap-2">
                  <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
                  <button type="button" @click="openEdit = null" class="border px-4 py-2 rounded">Batal</button>
                </div>
              </form>
            </div>
          </div>
        </template>
        @empty
        <tr>
          <td colspan="3" class="text-center px-4 py-6 text-gray-500 italic">Tidak ada data tim yang tersedia.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <!-- Modal Tambah -->
  <template x-if="openCreate">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <button @click="openCreate = false" class="absolute top-2 right-3 text-gray-600 hover:text-red-500 text-2xl">&times;</button>
        <h2 class="text-lg font-semibold mb-4">Tambah Jenis Tim</h2>
        <form action="{{ route('superadmin.jenis-tim.store') }}" method="POST">
          @csrf
          <input type="text" name="nama_tim" required placeholder="Nama Jenis Tim"
            class="w-full px-4 py-2 mb-4 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
          <div class="flex justify-end gap-2">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
            <button type="button" @click="openCreate = false" class="border px-4 py-2 rounded">Batal</button>
          </div>
        </form>
      </div>
    </div>
  </template>
</div>



<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('modalState', () => ({
      openCreate: false,
      openEdit: null,
    }));
  });
</script>
@endsection