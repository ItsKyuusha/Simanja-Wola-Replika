@extends('layouts.app')

@section('page-title', 'Master | User')

@section('content')
<div class="bg-white rounded-xl p-6 border border-gray-200">

  <!-- Header & Action -->
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 flex-wrap">
    <h2 class="text-2xl font-semibold text-gray-800">Manajemen User & Pegawai</h2>

    <div class="flex flex-col sm:flex-row items-center gap-3">
      <!-- Form Search -->
      <form method="GET" action="{{ route('superadmin.master_user.index') }}" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
          class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Cari nama pegawai, NIP, email...">
        <button type="submit"
          class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md text-gray-700 border border-gray-300">
          Cari
        </button>
      </form>

      <!-- Tombol Export -->
      <a href="{{ route('superadmin.master_user.export') }}"
        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
        Export Excel
      </a>

      <!-- Tombol Import -->
      <button @click="openImport = true"
        class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 transition">
        Import Excel
      </button>

      <!-- Tombol Tambah User -->
      <button @click="openCreate = true"
        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
        + Tambah User
      </button>
    </div>
  </div>

  <!-- Pesan Sukses -->
  @if(session('success'))
    <div class="mb-4 bg-green-50 text-green-700 px-4 py-2 rounded-md border border-green-200">
      {{ session('success') }}
    </div>
  @endif

  <!-- Tabel -->
  <div class="overflow-x-auto rounded-md border border-gray-200">
    <table class="w-full table-auto text-sm text-gray-700">
      <thead class="bg-blue-100 text-center text-sm text-gray-700">
        <tr>
          <th class="p-3 border">No.</th>
          <th class="p-3 border text-left">Nama Pegawai</th>
          <th class="p-3 border">NIP</th>
          <th class="p-3 border">Tim</th>
          <th class="p-3 border">Jabatan</th>
          <th class="p-3 border">Email</th>
          <th class="p-3 border">Role</th>
          <th class="p-3 border">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr class="even:bg-gray-50 hover:bg-gray-100 transition">
          <td class="p-3 border text-center">{{ $loop->iteration }}</td>
          <td class="p-3 border">{{ $user->pegawai->nama ?? '-' }}</td>
          <td class="p-3 border text-center">{{ $user->pegawai->nip ?? '-' }}</td>
          <td class="p-3 border text-center">{{ $user->pegawai->team->nama_tim ?? '-' }}</td>
          <td class="p-3 border text-center">{{ $user->pegawai->jabatan ?? '-' }}</td>
          <td class="p-3 border">{{ $user->email }}</td>
          <td class="p-3 border text-center capitalize">{{ $user->role }}</td>
          <td class="p-3 border">
            <div class="flex justify-center gap-2">
              <button @click="openEdit = {{ $user->id }}"
                class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 transition text-xs">
                Edit
              </button>
              <form action="{{ route('superadmin.master_user.destroy', $user->id) }}" method="POST"
                onsubmit="return confirm('Hapus user ini?')">
                @csrf @method('DELETE')
                <button type="submit"
                  class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition text-xs">
                  Hapus
                </button>
              </form>
            </div>
          </td>
        </tr>

        <!-- Modal Edit -->
        <template x-if="openEdit === {{ $user->id }}">
          <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-xl w-full max-w-md relative border border-gray-300">
              <button @click="openEdit = null"
                class="absolute top-2 right-2 text-gray-400 text-2xl hover:text-red-500">&times;</button>
              <h3 class="text-lg font-semibold mb-4 text-gray-700">Edit Data User</h3>
              <form action="{{ route('superadmin.master_user.update', $user->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 gap-3">
                  <input name="nama" class="border rounded px-3 py-2" value="{{ $user->pegawai->nama ?? '' }}" required>
                  <input name="nip" class="border rounded px-3 py-2" value="{{ $user->pegawai->nip ?? '' }}" required>
                  <input name="jabatan" class="border rounded px-3 py-2" value="{{ $user->pegawai->jabatan ?? '' }}" required>

                  <select name="team_id" class="border rounded px-3 py-2" required>
                    <option value="">-- Pilih Tim --</option>
                    @foreach($teams as $team)
                      <option value="{{ $team->id }}" {{ optional($user->pegawai->team)->id == $team->id ? 'selected' : '' }}>
                        {{ $team->nama_tim }}
                      </option>
                    @endforeach
                  </select>

                  <input type="text" name="name" class="border rounded px-3 py-2" value="{{ $user->name }}" required>
                  <input type="email" name="email" class="border rounded px-3 py-2" value="{{ $user->email }}" required>
                  <input type="password" name="password" class="border rounded px-3 py-2"
                    placeholder="Password baru (kosongkan jika tidak ganti)">
                  
                  <select name="role" class="border rounded px-3 py-2" required>
                    <option disabled>-- Pilih Role --</option>
                    <option value="superadmin" {{ $user->role === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                  </select>
                </div>
                <div class="mt-4 flex justify-end gap-2">
                  <button type="button" @click="openEdit = null"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Batal</button>
                  <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </template>

        @empty
        <tr>
          <td colspan="8" class="text-center py-6 text-gray-500">Tidak ada data user atau pegawai.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Import -->
<template x-if="openImport">
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl w-full max-w-md relative border border-gray-300">
      <button @click="openImport = false"
        class="absolute top-3 right-4 text-gray-400 text-2xl hover:text-red-500">&times;</button>
      <h2 class="text-xl font-semibold mb-4 text-gray-700">Import Data User & Pegawai</h2>
      <form action="{{ route('superadmin.master_user.import') }}" method="POST" enctype="multipart/form-data">
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

<!-- Modal Create -->
<template x-if="openCreate">
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl w-full max-w-xl relative border border-gray-300">
      <button @click="openCreate = false"
        class="absolute top-3 right-4 text-gray-400 text-2xl hover:text-red-500">&times;</button>
      <h2 class="text-xl font-semibold mb-4 text-gray-700">Tambah User & Pegawai</h2>
      <form action="{{ route('superadmin.master_user.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 gap-3">
          <input type="text" name="nama" class="border rounded px-3 py-2" placeholder="Nama Pegawai" required>
          <input type="text" name="nip" class="border rounded px-3 py-2" placeholder="NIP" required>
          <input type="text" name="jabatan" class="border rounded px-3 py-2" placeholder="Jabatan" required>

          <select name="team_id" class="border rounded px-3 py-2" required>
            <option disabled selected>-- Pilih Tim --</option>
            @foreach($teams as $team)
              <option value="{{ $team->id }}">{{ $team->nama_tim }}</option>
            @endforeach
          </select>

          <input type="text" name="name" class="border rounded px-3 py-2" placeholder="Nama User" required>
          <input type="email" name="email" class="border rounded px-3 py-2" placeholder="Email" required>
          <input type="password" name="password" class="border rounded px-3 py-2" placeholder="Password" required>

          <select name="role" class="border rounded px-3 py-2" required>
            <option disabled selected>-- Pilih Role --</option>
            <option value="superadmin">Superadmin</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
          </select>
        </div>
        <div class="mt-5 flex justify-end gap-2">
          <button type="button" @click="openCreate = false"
            class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Batal</button>
          <button type="submit"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</template>

<!-- Footer -->
<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('userModal', () => ({
      openCreate: false,
      openEdit: null,
      openImport: false,
    }));
  });
</script>
@endsection

@section('body-attrs', 'x-data="userModal()"')
