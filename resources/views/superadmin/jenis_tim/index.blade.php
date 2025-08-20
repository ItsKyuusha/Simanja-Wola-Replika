@extends('layouts.app')

@section('page-title', 'Master | Jenis TIM')

@section('content')
<div class="bg-white rounded-xl p-6 border border-gray-200" 
     x-data="{ openCreate: false, openEdit: null, openImport: false }">

  <!-- Header & Search -->
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 flex-wrap">
    <h2 class="text-2xl font-semibold text-gray-800">Tabel Jenis Tim</h2>

    <div class="flex flex-col sm:flex-row items-center gap-3">
      <!-- Form Search -->
      <form action="{{ route('superadmin.jenis-tim.index') }}" method="GET" class="flex w-full md:w-auto gap-2">
        <input
          type="text"
          name="search"
          class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Cari jenis tim..."
          value="{{ request('search') }}">
        <button
          type="submit"
          class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md text-gray-700 border border-gray-300">
          Cari
        </button>
      </form>

      <!--Tombol eksport -->
      <a href="{{ route('superadmin.jenis-tim.export') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
        Export Excel</a>

      <!--Tombol Import -->
      <button @click="openImport = true"
        class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 transition">
        Import Excel
      </button>

      <!-- Tambah Button -->
      <button
        @click="openCreate = true"
        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
        + Tambah Jenis Tim
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
          <td class="px-4 py-2 border">
            <div class="flex justify-center items-center gap-2">
              <button
                @click="openEdit = {{ $tim->id }}"
                class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-3 py-1 rounded">
                Edit
              </button>
              <form
                action="{{ route('superadmin.jenis-tim.destroy', $tim->id) }}"
                method="POST"
                onsubmit="return confirm('Yakin hapus data ini?')">
                @csrf
                @method('DELETE')
                <button
                  type="submit"
                  class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1 rounded">
                  Hapus
                </button>
              </form>
            </div>
          </td>
        </tr>

        <!-- Modal Edit -->
        <template x-if="openEdit === {{ $tim->id }}">
          <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
              <button
                @click="openEdit = null"
                class="absolute top-2 right-3 text-gray-600 hover:text-red-500 text-2xl">
                &times;
              </button>
              <h2 class="text-lg font-semibold mb-4">Edit Jenis Tim</h2>
              <form action="{{ route('superadmin.jenis-tim.update', $tim->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input
                  type="text"
                  name="nama_tim"
                  value="{{ $tim->nama_tim }}"
                  required
                  class="w-full px-4 py-2 mb-4 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                <div class="flex justify-end gap-2">
                  <button
                    type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Simpan
                  </button>
                  <button
                    type="button"
                    @click="openEdit = null"
                    class="border px-4 py-2 rounded">
                    Batal
                  </button>
                </div>
              </form>
            </div>
          </div>
        </template>
        @empty
        <tr>
          <td colspan="3" class="text-center px-4 py-6 text-gray-500 italic">
            Tidak ada data tim yang tersedia.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Modal Import -->
<template x-if="openImport">
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl w-full max-w-md relative border border-gray-300">
      <button @click="openImport = false"
        class="absolute top-3 right-4 text-gray-400 text-2xl hover:text-red-500">&times;</button>
      <h2 class="text-xl font-semibold mb-4 text-gray-700">Import Data Jenis Tim</h2>
      <form action="{{ route('superadmin.jenis-tim.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".xlsx,.xls" required
          class="border w-full rounded px-3 py-2 mb-3">
        <div class="mt-5 flex justify-end gap-2">
          <button type="button" @click="openImport = false"
            class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Batal</button>
          <button type="submit"
            class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Upload</button>
        </div>
      </form>
    </div>
  </div>
</template>


  <!-- Modal Tambah -->
  <template x-if="openCreate">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
        <button
          @click="openCreate = false"
          class="absolute top-2 right-3 text-gray-600 hover:text-red-500 text-2xl">
          &times;
        </button>
        <h2 class="text-lg font-semibold mb-4">Tambah Jenis Tim</h2>
        <form action="{{ route('superadmin.jenis-tim.store') }}" method="POST">
          @csrf
          <input
            type="text"
            name="nama_tim"
            required
            placeholder="Nama Jenis Tim"
            class="w-full px-4 py-2 mb-4 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
          <div class="flex justify-end gap-2">
            <button
              type="submit"
              class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
              Simpan
            </button>
            <button
              type="button"
              @click="openCreate = false"
              class="border px-4 py-2 rounded">
              Batal
            </button>
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