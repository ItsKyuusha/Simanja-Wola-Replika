@extends('layouts.app')
@section('title', 'Tugas Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ openModal: null }">

  <div class="bg-white shadow rounded-lg p-6">
    <h3 class="text-2xl font-semibold mb-4">Daftar Tugas Anda</h3>

    <!-- Form Search -->
    <form method="GET" action="{{ route('user.pekerjaan.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
      <div class="md:col-span-4">
        <input type="text" name="search" value="{{ request('search') }}" class="w-full border rounded px-3 py-2 text-sm" placeholder="Cari Nama Tugas...">
      </div>

      <div class="md:col-span-2">
        <input type="text" name="jenis_pekerjaan" value="{{ request('jenis_pekerjaan') }}" class="w-full border rounded px-3 py-2 text-sm" placeholder="Cari Jenis Pekerjaan...">
      </div>

      <div class="md:col-span-3">
        <input type="text" name="deadline" value="{{ request('deadline') }}" class="w-full border rounded px-3 py-2 text-sm" placeholder="Cari Deadline (e.g. 01 Jan - 31 Mar)">
      </div>

      <div class="md:col-span-2">
        <select name="status" class="w-full border rounded px-3 py-2 text-sm">
          <option value="">Pilih Status</option>
          <option value="belum_dikerjakan" {{ request('status') == 'belum_dikerjakan' ? 'selected' : '' }}>Belum Dikerjakan</option>
          <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
          <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
        </select>
      </div>

      <div class="md:col-span-1">
        <button type="submit" class="w-full bg-blue-600 text-white rounded px-4 py-2 text-sm hover:bg-blue-700">Cari</button>
      </div>
    </form>

    @forelse($tugas as $t)
      <div class="bg-gray-50 border rounded-lg shadow mb-6 overflow-hidden">
        <div class="bg-blue-600 text-white px-4 py-2 flex flex-wrap justify-between items-center gap-2">
          <strong class="text-sm">{{ $t->nama_tugas }} - {{ $t->jenisPekerjaan->nama_pekerjaan }}</strong>

          <div class="flex items-center gap-2">
            @if(!$t->realisasi)
              <span class="bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded">Belum Dikerjakan</span>
              <button @click="openModal = 'add-{{ $t->id }}'" class="bg-white text-blue-600 text-xs px-3 py-1 rounded hover:bg-blue-100">Isi Realisasi</button>
            @elseif($t->realisasi->realisasi < $t->target)
              <span class="bg-yellow-300 text-yellow-900 text-xs px-2 py-1 rounded">Ongoing</span>
              <button @click="openModal = 'edit-{{ $t->realisasi->id }}'" class="bg-yellow-500 text-white text-xs px-3 py-1 rounded hover:bg-yellow-600">Edit Realisasi</button>
            @else
              <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">Selesai</span>
              <button @click="openModal = 'edit-{{ $t->realisasi->id }}'" class="bg-yellow-500 text-white text-xs px-3 py-1 rounded hover:bg-yellow-600">Edit Realisasi</button>
            @endif
          </div>
        </div>

        <div class="p-4 text-sm">
          <p><strong>Target:</strong> {{ $t->target }} {{ $t->satuan }}</p>
          <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y') }}</p>

          @if($t->realisasi)
            <div class="bg-green-100 border-l-4 border-green-500 p-3 rounded mt-3 text-xs">
              <strong>Sudah Dikerjakan</strong><br>
              Realisasi: {{ $t->realisasi->realisasi }}<br>
              Tanggal: {{ $t->realisasi->tanggal_realisasi }}<br>
              Kualitas: {{ $t->realisasi->nilai_kualitas }} | Kuantitas: {{ $t->realisasi->nilai_kuantitas }}<br>
              @if($t->realisasi->file_bukti)
                <a href="{{ asset('storage/'.$t->realisasi->file_bukti) }}" target="_blank" class="text-blue-600 underline">Lihat Bukti</a>
              @endif
            </div>
          @endif
        </div>
      </div>

      {{-- safe route resolution (store) --}}
      @php
        if (Route::has('user.pekerjaan.realisasi')) {
            $storeAction = route('user.pekerjaan.realisasi', $t->id);
        } elseif (Route::has('user.realisasi.store')) {
            $storeAction = route('user.realisasi.store', $t->id);
        } elseif (Route::has('user.pekerjaan.realisasi.store')) {
            $storeAction = route('user.pekerjaan.realisasi.store', $t->id);
        } else {
            // fallback URL — adjust path if your route path differs
            $storeAction = url('user/pekerjaan/'.$t->id.'/realisasi');
        }
      @endphp

      {{-- modal add --}}
      <div x-show="openModal === 'add-{{ $t->id }}'" x-cloak @keydown.escape.window="openModal = null" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display:none;" @click.self="openModal = null">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 mx-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Isi Realisasi - {{ $t->nama_tugas }}</h3>
            <button @click="openModal = null" class="text-gray-600 hover:text-gray-800">✕</button>
          </div>

          <form action="{{ $storeAction }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="tugas_id" value="{{ $t->id }}">

            <label class="block text-sm font-medium">Realisasi</label>
            <input type="number" name="realisasi" required class="w-full border rounded px-3 py-2 mb-3 text-sm">

            <label class="block text-sm font-medium">Tanggal Realisasi</label>
            <input type="date" name="tanggal_realisasi" required class="w-full border rounded px-3 py-2 mb-3 text-sm">

            <label class="block text-sm font-medium">Nilai Kualitas</label>
            <input type="number" name="nilai_kualitas" min="0" max="100" class="w-full border rounded px-3 py-2 mb-3 text-sm">

            <label class="block text-sm font-medium">Nilai Kuantitas</label>
            <input type="number" name="nilai_kuantitas" min="0" max="100" class="w-full border rounded px-3 py-2 mb-3 text-sm">

            <label class="block text-sm font-medium">Catatan</label>
            <textarea name="catatan" class="w-full border rounded px-3 py-2 mb-3 text-sm"></textarea>

            <label class="block text-sm font-medium">Upload Bukti (PDF, Gambar)</label>
            <input type="file" name="file_bukti" accept=".pdf,image/*" class="w-full mb-4">

            <div class="flex justify-end gap-2">
              <button type="button" @click="openModal = null" class="px-4 py-2 bg-gray-200 rounded text-sm">Batal</button>
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Kirim Realisasi</button>
            </div>
          </form>
        </div>
      </div>

      {{-- modal edit (only if realisasi exists) --}}
      @if($t->realisasi)
        @php
          if (Route::has('user.pekerjaan.realisasi.update')) {
              $updateAction = route('user.pekerjaan.realisasi.update', $t->realisasi->id);
          } elseif (Route::has('user.realisasi.update')) {
              $updateAction = route('user.realisasi.update', $t->realisasi->id);
          } elseif (Route::has('user.pekerjaan.realisasi.update')) {
              $updateAction = route('user.pekerjaan.realisasi.update', $t->realisasi->id);
          } else {
              // fallback URL — adjust path if your route path differs
              $updateAction = url('user/pekerjaan/realisasi/'.$t->realisasi->id);
          }
        @endphp

        <div x-show="openModal === 'edit-{{ $t->realisasi->id }}'" x-cloak @keydown.escape.window="openModal = null" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display:none;" @click.self="openModal = null">
          <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 mx-4">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold">Edit Realisasi - {{ $t->nama_tugas }}</h3>
              <button @click="openModal = null" class="text-gray-600 hover:text-gray-800">✕</button>
            </div>

            <form action="{{ $updateAction }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')
              <input type="hidden" name="tugas_id" value="{{ $t->id }}">

              <label class="block text-sm font-medium">Realisasi</label>
              <input type="number" name="realisasi" value="{{ $t->realisasi->realisasi }}" required class="w-full border rounded px-3 py-2 mb-3 text-sm">

              <label class="block text-sm font-medium">Tanggal Realisasi</label>
              <input type="date" name="tanggal_realisasi" value="{{ $t->realisasi->tanggal_realisasi }}" required class="w-full border rounded px-3 py-2 mb-3 text-sm">

              <label class="block text-sm font-medium">Nilai Kualitas</label>
              <input type="number" name="nilai_kualitas" min="0" max="100" value="{{ $t->realisasi->nilai_kualitas }}" class="w-full border rounded px-3 py-2 mb-3 text-sm">

              <label class="block text-sm font-medium">Nilai Kuantitas</label>
              <input type="number" name="nilai_kuantitas" min="0" max="100" value="{{ $t->realisasi->nilai_kuantitas }}" class="w-full border rounded px-3 py-2 mb-3 text-sm">

              <label class="block text-sm font-medium">Catatan</label>
              <textarea name="catatan" class="w-full border rounded px-3 py-2 mb-3 text-sm">{{ $t->realisasi->catatan }}</textarea>

              <label class="block text-sm font-medium">Upload Bukti (PDF, Gambar)</label>
              <input type="file" name="file_bukti" accept=".pdf,image/*" class="w-full mb-3 text-sm">
              @if($t->realisasi->file_bukti)
                <p class="text-xs text-gray-600 mb-3">File saat ini: <a href="{{ asset('storage/'.$t->realisasi->file_bukti) }}" target="_blank" class="text-blue-600 underline">Lihat Bukti</a></p>
              @endif

              <div class="flex justify-end gap-2">
                <button type="button" @click="openModal = null" class="px-4 py-2 bg-gray-200 rounded text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600">Update</button>
              </div>
            </form>
          </div>
        </div>
      @endif

    @empty
      <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded text-sm">
        Tidak ada tugas yang tersedia saat ini.
      </div>
    @endforelse

  </div>
</div>

