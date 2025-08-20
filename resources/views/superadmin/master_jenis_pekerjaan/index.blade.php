@extends('layouts.app')

@section('page-title', 'Master | Jenis Pekerjaan')

@section('content')
<div x-data="{ openCreate: false, openEdit: null, openImport: false }" class="p-6 bg-white shadow rounded-xl">

  <!-- Header -->
  <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
    <h2 class="text-xl font-semibold text-gray-800">Tabel Jenis Pekerjaan</h2>

    <div class="flex flex-wrap gap-2">
      <!-- Search Form -->
      <form method="GET" action="{{ route('superadmin.jenis-pekerjaan.index') }}" class="flex items-center gap-2">
        <input type="text" name="search" class="w-full sm:w-72 border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200" placeholder="Cari nama pekerjaan..." value="{{ request('search') }}" >
        <button type="submit" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Cari</button>
      </form>

      @if(auth()->user()?->role === 'superadmin')
        <a href="{{ route('superadmin.jenis-pekerjaan.export') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">Export Excel</a>

        <button @click="openImport = true" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 transition">Import Excel</button>
      @endif

      <button @click="openCreate = true" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">+ Tambah Jenis Pekerjaan</button>
    </div>
  </div>

  <!-- Notifikasi -->
  @if(session('success'))
    <div class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="mb-4 bg-red-100 text-red-700 px-4 py-2 rounded">
      <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Tabel -->
  <div class="overflow-x-auto">
    <table class="min-w-full border border-gray-300 text-sm">
      <thead class="bg-blue-100 text-center text-sm text-gray-700">
        <tr>
          <th class="border px-3 py-2">No.</th>
          <th class="border px-3 py-2">Nama</th>
          <th class="border px-3 py-2">Satuan</th>
          <th class="border px-3 py-2">Bobot</th>
          <th class="border px-3 py-2">Tim</th>
          <th class="border px-3 py-2">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $item)
        <tr class="even:bg-gray-50">
          <td class="border px-3 py-2 text-center">{{ $loop->iteration }}</td>
          <td class="border px-3 py-2">{{ $item->nama_pekerjaan }}</td>
          <td class="border px-3 py-2 text-center">{{ $item->satuan }}</td>
          <td class="border px-3 py-2 text-center">{{ $item->bobot }}</td>
          <td class="border px-3 py-2">{{ $item->team->nama_tim ?? '-' }}</td>
          <td class="border px-3 py-2 text-center space-x-2">
            <button @click="openEdit = {{ $item->id }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm">Edit</button>
            <form action="{{ route('superadmin.jenis-pekerjaan.destroy', $item->id) }}" method="POST" class="inline">
              @csrf @method('DELETE')
              <button onclick="return confirm('Hapus jenis pekerjaan ini?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Hapus</button>
            </form>
          </td>
        </tr>

        <!-- Modal Edit -->
        <template x-if="openEdit === {{ $item->id }}">
          <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md relative">
              <button @click="openEdit = null" class="absolute top-2 right-2 text-gray-500 text-2xl hover:text-red-500">&times;</button>
              <h3 class="text-lg font-semibold mb-4">Edit Jenis Pekerjaan</h3>
              <form action="{{ route('superadmin.jenis-pekerjaan.update', $item->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 gap-3">
                  <input name="nama_pekerjaan" class="border rounded px-3 py-2" value="{{ $item->nama_pekerjaan }}" required>
                  <input name="satuan" class="border rounded px-3 py-2" value="{{ $item->satuan }}" required>
                  <input name="bobot" type="number" step="any" class="border rounded px-3 py-2" value="{{ $item->bobot }}" required>
                  <select name="tim_id" class="border rounded px-3 py-2" required>
                    <option value="">-- Pilih Tim --</option>
                    @foreach($teams as $team)
                      <option value="{{ $team->id }}" {{ $item->tim_id == $team->id ? 'selected' : '' }}>{{ $team->nama_tim }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="mt-4 flex justify-end gap-2">
                  <button type="button" @click="openEdit = null" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                  <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </template>
        @empty
        <tr>
          <td colspan="6" class="text-center text-gray-500 py-4">Tidak ada data jenis pekerjaan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Modal Create -->
  <template x-if="openCreate">
    <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg w-full max-w-md relative">
         <button @click="openCreate = false" class="absolute top-3 right-4 text-gray-400 text-2xl hover:text-red-500">&times;</button>
        <h3 class="text-lg font-semibold mb-4">Tambah Jenis Pekerjaan</h3>
        <form action="{{ route('superadmin.jenis-pekerjaan.store') }}" method="POST">
          @csrf
          <div class="grid grid-cols-1 gap-3">
            <input name="nama_pekerjaan" class="border rounded px-3 py-2" placeholder="Nama Pekerjaan" required>
            <input name="satuan" class="border rounded px-3 py-2" placeholder="Satuan" required>
            <input name="bobot" type="number" step="any" class="border rounded px-3 py-2" placeholder="Bobot" required>
            <select name="tim_id" class="border rounded px-3 py-2" required>
              <option value="">-- Pilih Tim --</option>
              @foreach($teams as $team)
                <option value="{{ $team->id }}">{{ $team->nama_tim }}</option>
              @endforeach
            </select>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
            <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </template>

  <!-- Modal Import -->
  <template x-if="openImport">
    <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg w-full max-w-md relative">
         <button @click="openImport = false" class="absolute top-3 right-4 text-gray-400 text-2xl hover:text-red-500">&times;</button>
        <h3 class="text-lg font-semibold mb-4">Import Jenis Pekerjaan dari Excel</h3>
        <form action="{{ route('superadmin.jenis-pekerjaan.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="file" name="file" accept=".xlsx,.xls" class="border rounded px-3 py-2 w-full" required>
          <div class="mt-4 flex justify-end gap-2">
            <button type="button" @click="openImport = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
            <button class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Upload</button>
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

@endsection

@section('body-attrs', 'x-data')
