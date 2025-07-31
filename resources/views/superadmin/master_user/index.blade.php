@extends('layouts.app')

@section('page-title', 'Master | User')


@section('content')
<div class="bg-white shadow rounded-xl p-6">
  <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
    <h2 class="text-2xl font-semibold text-gray-800">📋 Manajemen User & Pegawai</h2>
    <button
      @click="openCreate = true"
      type="button"
      class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
      + Tambah User
    </button>
  </div>

  <!-- Search Form -->
  <form method="GET" action="{{ route('superadmin.master_user.index') }}" class="mb-5">
    <div class="flex flex-col sm:flex-row items-center gap-3">
      <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        class="w-full sm:w-72 border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200"
        placeholder="🔍 Cari nama pegawai, NIP, email...">
      <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md text-gray-700 border border-gray-300">
        Cari
      </button>
    </div>
  </form>

  <!-- Success Message -->
  @if(session('success'))
  <div class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded-md border border-green-200">
    {{ session('success') }}
  </div>
  @endif

  <!-- Table -->
  <div class="overflow-x-auto rounded-md border border-gray-200">
    <table class="w-full table-auto text-sm text-gray-800">
      <thead class="bg-gray-50 text-gray-700 font-semibold text-center uppercase tracking-wide">
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
        <tr class="hover:bg-gray-50 even:bg-gray-100 transition">
          <td class="p-3 border text-center">{{ $loop->iteration }}</td>
          <td class="p-3 border">{{ $user->pegawai->nama ?? '-' }}</td>
          <td class="p-3 border text-center">{{ $user->pegawai->nip ?? '-' }}</td>
          <td class="p-3 border text-center">{{ $user->pegawai->team->nama_tim ?? '-' }}</td>
          <td class="p-3 border text-center">{{ $user->pegawai->jabatan ?? '-' }}</td>
          <td class="p-3 border">{{ $user->email }}</td>
          <td class="p-3 border capitalize text-center">{{ $user->role }}</td>
          <td class="p-3 border">
            <div class="flex gap-2 justify-center">
              <!-- Edit button -->
              <button
                @click="openEdit = {{ $user->id }}"
                type="button"
                class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 transition text-xs">
                Edit
              </button>

              <!-- Delete button -->
              <form action="{{ route('superadmin.master_user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition text-xs">
                  Hapus
                </button>
              </form>
            </div>
          </td>
        </tr>
        <!-- Modal Edit -->
        <template x-if="openEdit === {{ $user->id }}">
          <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-md relative">
              <button @click="openEdit = null" class="absolute top-2 right-2 text-gray-500 text-2xl hover:text-red-500">&times;</button>
              <h3 class="text-lg font-semibold mb-4">Edit Data User</h3>
              <form action="{{ route('superadmin.master_user.update', $user->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 gap-3">
                  <input name="nama" class="border rounded px-3 py-2" value="{{ $user->pegawai->nama ?? '' }}" placeholder="Nama Pegawai" required>
                  <input name="nip" class="border rounded px-3 py-2" value="{{ $user->pegawai->nip ?? '' }}" placeholder="NIP" required>
                  <input name="jabatan" class="border rounded px-3 py-2" value="{{ $user->pegawai->jabatan ?? '' }}" placeholder="Jabatan" required>

                  <select name="team_id" class="border rounded px-3 py-2" required>
                    <option value="">-- Pilih Tim --</option>
                    @foreach($teams as $team)
                    <option value="{{ $team->id }}" {{ optional($user->pegawai->team)->id == $team->id ? 'selected' : '' }}>
                      {{ $team->nama_tim }}
                    </option>
                    @endforeach
                  </select>

                  <input type="text" name="name" class="border rounded px-3 py-2" value="{{ $user->name }}" placeholder="Nama User" required>
                  <input type="email" name="email" class="border rounded px-3 py-2" value="{{ $user->email }}" placeholder="Email" required>

                  <select name="role" class="border rounded px-3 py-2" required>
                    <option disabled>-- Pilih Role --</option>
                    <option value="superadmin" {{ $user->role === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
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
          <td colspan="8" class="text-center py-5 text-gray-500">Tidak ada data user atau pegawai.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Create Modal -->
<template x-if="openCreate">
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-xl relative shadow-xl">
      <button @click="openCreate = false" class="absolute top-3 right-4 text-gray-400 text-2xl hover:text-red-500">&times;</button>
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
          <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 text-gray-600 rounded hover:bg-gray-200">Batal</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</template>

<!-- Alpine.js Script -->
<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('userModal', () => ({
      openCreate: false,
      openEdit: null,
    }));
  });
</script>
@endsection

@section('body-attrs', 'x-data="userModal()"')