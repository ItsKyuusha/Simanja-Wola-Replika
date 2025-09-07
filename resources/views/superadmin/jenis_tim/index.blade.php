@extends('layouts.app')

@section('page-title', 'Master | Jenis TIM')

@section('content')
<div class="bg-white rounded-2xl p-6 mb-12 border border-gray-200" x-data="modalState">

  <!-- Header & Search -->
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 flex-wrap">
    <h2 class="text-2xl font-semibold text-blue-600">Tabel Jenis Tim</h2>
    <div class="flex flex-col sm:flex-row items-center gap-3">
      <!-- Form Search -->
      <form method="GET" action="{{ route('superadmin.jenis-tim.index') }}" class="flex gap-3 w-full sm:w-auto">
        <input type="text" name="search" value="{{ request('search') }}"
          class="px-4 py-2 w-full sm:w-64 border border-gray-300 rounded-lg 
                 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                 bg-white/50 backdrop-blur-sm placeholder-gray-500"
          placeholder="Cari jenis tim">
        <button type="submit"
          class="px-4 py-2 rounded-lg border border-gray-400 text-gray-600 font-medium 
                 bg-white/40 backdrop-blur-sm hover:bg-gray-100 hover:text-gray-700
                 transition duration-200 ease-in-out transform hover:scale-105">
          <i class="fas fa-search mr-1"></i> Cari
        </button>
      </form>

      <!-- Tombol Export -->
      <a href="{{ route('superadmin.jenis-tim.export') }}"
        class="inline-flex items-center px-4 py-2 rounded-lg border border-green-400 text-green-600 font-medium
               bg-green-200/20 backdrop-blur-sm shadow-sm 
               hover:bg-green-300/30 hover:border-green-500 hover:text-green-700
               transition duration-200 ease-in-out transform hover:scale-105">
        <i class="fas fa-file-excel mr-2"></i> Export Tabel
      </a>

      <!-- Tombol Import -->
      <button @click="openImport = true"
        class="inline-flex items-center px-4 py-2 rounded-lg border border-purple-400 text-purple-600 font-medium
               bg-purple-200/20 backdrop-blur-sm shadow-sm 
               hover:bg-purple-300/30 hover:border-purple-500 hover:text-purple-700
               transition duration-200 ease-in-out transform hover:scale-105">
        <i class="fas fa-file-upload mr-2"></i> Upload Data
      </button>

      <!-- Tombol Tambah -->
      <button @click="openCreate = true"
        class="inline-flex items-center px-4 py-2 rounded-lg border border-blue-500 text-blue-600 font-medium
               bg-blue-200/20 backdrop-blur-sm shadow-sm 
               hover:bg-blue-300/30 hover:border-blue-600 hover:text-blue-700
               transition duration-200 ease-in-out transform hover:scale-105">
        <i class="fas fa-user-plus mr-2"></i> Tambah Jenis Tim
      </button>
    </div>
  </div>

  <!-- Success Message -->
  @if(session('success'))
  <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-md">
    {{ session('success') }}
  </div>
  @endif

  <!-- Table -->
  <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
    <table class="w-full table-auto text-sm text-gray-700">
      <thead class="bg-gradient-to-r from-blue-100 to-blue-200 text-center text-sm text-gray-700">
        <tr>
          <th class="p-3 border">No</th>
          <th class="p-3 border text-left">Jenis Tim</th>
          <th class="p-3 border">Ketua Tim</th>
          <th class="p-3 border">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $tim)
        <tr class="even:bg-gray-50 hover:bg-blue-50 transition">
          <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
          <td class="text-left px-4 py-2 border">{{ $tim->nama_tim }}</td>
          <td class="text-left px-4 py-2 border">
            @if($tim->pegawais && $tim->pegawais->count())
            @foreach($tim->pegawais as $pegawai)
            @if($pegawai->pivot->is_leader)
            <span class="inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs m-0.5">
              {{ $pegawai->nama }}
            </span>
            @endif
            @endforeach
            @else
            -
            @endif
          </td>
          <td class="px-4 py-2 border">
            <div class="flex justify-center items-center gap-2">
              <button @click="openEdit = {{ $tim->id }}"
                class="px-3 py-1 rounded-lg border border-yellow-400 text-yellow-600 bg-yellow-100/40 backdrop-blur-sm text-xs
                       hover:bg-yellow-200 hover:text-yellow-700 transition">
                <i class="fas fa-edit mr-1"></i> Edit
              </button>
              <form action="{{ route('superadmin.jenis-tim.destroy', $tim->id) }}" method="POST"
                onsubmit="return confirm('Hapus tim ini?')">
                @csrf @method('DELETE')
                <button type="submit"
                  class="px-3 py-1 rounded-lg border border-red-500 text-red-600 bg-red-100/40 backdrop-blur-sm text-xs
                         hover:bg-red-200 hover:text-red-700 transition">
                  <i class="fas fa-trash mr-1"></i> Hapus
                </button>
              </form>
            </div>
          </td>
        </tr>

        <!-- Modal Edit -->
        <template x-if="openEdit === {{ $tim->id }}">
          <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white/90 backdrop-blur-md p-6 rounded-2xl w-full max-w-md relative border border-gray-200 shadow-xl">
              <button @click="openEdit = null" class="absolute top-3 right-4 text-gray-400 text-2xl hover:text-red-500">&times;</button>
              <h3 class="text-lg font-semibold mb-4 text-gray-700">Edit Jenis Tim</h3>
              <form action="{{ route('superadmin.jenis-tim.update', $tim->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="text" name="nama_tim" value="{{ $tim->nama_tim }}" required
                  class="w-full px-4 py-2 mb-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                @error('nama_tim')
                <p class="text-sm text-red-600 mb-2">{{ $message }}</p>
                @enderror
                <div class="mt-4 flex justify-end gap-2">
                  <button type="submit"
                    class="px-4 py-2 rounded-lg border border-green-500 bg-green-100/60 text-green-700 hover:bg-green-200 transition">
                    Simpan
                  </button>
                  <button type="button" @click="openEdit = null"
                    class="px-4 py-2 rounded-lg border border-gray-400 bg-gray-100/60 text-gray-700 hover:bg-gray-200 transition">
                    Batal
                  </button>
                </div>
              </form>
            </div>
          </div>
        </template>
        @empty
        <tr>
          <td colspan="4" class="text-center px-4 py-6 text-gray-500 italic">
            Tidak ada data tim yang tersedia.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Modal Import -->
  <template x-if="openImport">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-xl w-full max-w-md relative border border-gray-300 shadow-xl">
        <button @click="openImport = false" class="absolute top-3 right-4 text-gray-400 text-2xl hover:text-red-500">&times;</button>
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Import Data Jenis Tim</h2>
        <form action="{{ route('superadmin.jenis-tim.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="file" name="file" accept=".xlsx,.xls" required class="border w-full rounded px-3 py-2 mb-3">
          @error('file')
          <p class="text-sm text-red-600 mb-2">{{ $message }}</p>
          @enderror
          <div class="mt-5 flex justify-end gap-2">
            <button type="button" @click="openImport = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Batal</button>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Upload</button>
          </div>
        </form>
      </div>
    </div>
  </template>

  <!-- Modal Tambah -->
  <template x-if="openCreate">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 relative border border-gray-300">
        <button @click="openCreate = false" class="absolute top-3 right-4 text-gray-400 hover:text-red-500 text-2xl">&times;</button>
        <h2 class="text-lg font-semibold mb-4">Tambah Jenis Tim</h2>
        <form action="{{ route('superadmin.jenis-tim.store') }}" method="POST">
          @csrf
          <input type="text" name="nama_tim" required placeholder="Nama Jenis Tim"
            class="w-full px-4 py-2 mb-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
          @error('nama_tim')
          <p class="text-sm text-red-600 mb-2">{{ $message }}</p>
          @enderror
          <div class="flex justify-end gap-2 mt-4">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
            <button type="button" @click="openCreate = false" class="border px-4 py-2 rounded">Batal</button>
          </div>
        </form>
      </div>
    </div>
  </template>
</div>

<!-- Footer -->
<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('modalState', () => ({
      openCreate: false,
      openEdit: null,
      openImport: false,
    }));
  });
</script>
@endsection