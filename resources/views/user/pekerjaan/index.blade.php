@extends('layouts.app')
@section('page-title', 'Tugas Saya')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
  <h3 class="text-lg font-semibold mb-4">Daftar Tugas Anda</h3>

  <!-- Form Search -->
  <!-- Form Search -->
  <form method="GET" action="{{ route('user.pekerjaan.index') }}" class="flex flex-wrap gap-3 mb-6 items-center">
    <!-- Cari Nama Tugas -->
    <input type="text"
      name="search"
      value="{{ request('search') }}"
      placeholder="Cari Nama Tugas..."
      class="w-[300px] px-3 py-2 border rounded" />

    <!-- Cari Jenis Pekerjaan -->
    <input type="text"
      name="jenis_pekerjaan"
      value="{{ request('jenis_pekerjaan') }}"
      placeholder="Cari Jenis Pekerjaan..."
      class="w-[300px] px-3 py-2 border rounded" />

    <!-- Deadline (Tanggal Mulai & Akhir) -->
    <input type="date"
      name="deadline_start"
      value="{{ request('deadline_start') }}"
      class="px-3 py-2 border rounded" />

    <input type="date"
      name="deadline_end"
      value="{{ request('deadline_end') }}"
      class="px-3 py-2 border rounded" />

    <!-- Status -->
    <select name="status" class="w-[150px] px-3 py-2 border rounded">
      <option value="">Pilih Status</option>
      <option value="belum_dikerjakan" {{ request('status') == 'belum_dikerjakan' ? 'selected' : '' }}>Belum Dikerjakan</option>
      <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
      <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
    </select>

    <!-- Tombol -->
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded px-4 py-2">
      Cari
    </button>
  </form>



  @forelse($tugas as $t)
  <div class="border rounded-lg mb-4 overflow-hidden">
    <div class="bg-blue-500 text-white px-4 py-3 flex justify-between items-center">
      <span class="font-semibold">{{ $t->nama_tugas }} - {{ $t->jenisPekerjaan->nama_pekerjaan }}</span>
      <div class="flex items-center gap-2">
        @if(!$t->realisasi)
        <span class="bg-gray-300 text-gray-800 text-xs px-2 py-1 rounded">Belum Dikerjakan</span>
        <button class="bg-white text-gray-800 px-3 py-1 rounded text-sm" onclick="document.getElementById('modalRealisasi{{ $t->id }}').classList.remove('hidden')">Isi Realisasi</button>
        @elseif($t->realisasi->realisasi < $t->target)
          <span class="bg-yellow-300 text-yellow-900 text-xs px-2 py-1 rounded">Ongoing</span>
          <button class="bg-yellow-500 text-white px-3 py-1 rounded text-sm" onclick="document.getElementById('modalEditRealisasi{{ $t->realisasi->id }}').classList.remove('hidden')">Edit Realisasi</button>
          @else
          <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">Selesai Dikerjakan</span>
          <button class="bg-yellow-500 text-white px-3 py-1 rounded text-sm" onclick="document.getElementById('modalEditRealisasi{{ $t->realisasi->id }}').classList.remove('hidden')">Edit Realisasi</button>
          @endif
      </div>
    </div>
    <div class="p-4">
      <p><strong>Target:</strong> {{ $t->target }} {{ $t->satuan }}</p>
      <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y') }}</p>

      @if($t->realisasi)
      <div class="bg-green-100 border border-green-300 text-green-800 p-3 mt-2 rounded">
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

  <!-- Modal Realisasi -->
  @if(!$t->realisasi)
  <div id="modalRealisasi{{ $t->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
      <h5 class="text-lg font-semibold mb-4">Realisasi Tugas: {{ $t->nama_tugas }}</h5>
      <form action="{{ route('user.pekerjaan.realisasi', $t->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <input type="number" name="realisasi" placeholder="Realisasi" class="w-full border rounded px-3 py-2" required>
        <input type="date" name="tanggal_realisasi" class="w-full border rounded px-3 py-2" required>
        <input type="number" name="nilai_kualitas" placeholder="Nilai Kualitas" class="w-full border rounded px-3 py-2" min="0" max="100">
        <input type="number" name="nilai_kuantitas" placeholder="Nilai Kuantitas" class="w-full border rounded px-3 py-2" min="0" max="100">
        <textarea name="catatan" placeholder="Catatan" class="w-full border rounded px-3 py-2"></textarea>
        <input type="file" name="file_bukti" accept=".pdf,image/*" class="w-full border rounded px-3 py-2">
        <div class="flex justify-end gap-2">
          <button type="button" class="bg-gray-300 px-4 py-2 rounded" onclick="document.getElementById('modalRealisasi{{ $t->id }}').classList.add('hidden')">Batal</button>
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Kirim Realisasi</button>
        </div>
      </form>
    </div>
  </div>
  @endif

  <!-- Modal Edit -->
  @if($t->realisasi)
  <div id="modalEditRealisasi{{ $t->realisasi->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
      <h5 class="text-lg font-semibold mb-4">Edit Realisasi Tugas: {{ $t->nama_tugas }}</h5>
      <form action="{{ route('user.pekerjaan.realisasi.update', $t->realisasi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')
        <input type="number" name="realisasi" value="{{ $t->realisasi->realisasi }}" class="w-full border rounded px-3 py-2" required>
        <input type="date" name="tanggal_realisasi" value="{{ $t->realisasi->tanggal_realisasi }}" class="w-full border rounded px-3 py-2" required>
        <input type="number" name="nilai_kualitas" value="{{ $t->realisasi->nilai_kualitas }}" class="w-full border rounded px-3 py-2" min="0" max="100">
        <input type="number" name="nilai_kuantitas" value="{{ $t->realisasi->nilai_kuantitas }}" class="w-full border rounded px-3 py-2" min="0" max="100">
        <textarea name="catatan" class="w-full border rounded px-3 py-2">{{ $t->realisasi->catatan }}</textarea>
        <input type="file" name="file_bukti" accept=".pdf,image/*" class="w-full border rounded px-3 py-2">
        @if($t->realisasi->file_bukti)
        <small class="text-gray-500">File saat ini: <a href="{{ asset('storage/'.$t->realisasi->file_bukti) }}" class="text-blue-600 underline" target="_blank">Lihat Bukti</a></small>
        @endif
        <div class="flex justify-end gap-2">
          <button type="button" class="bg-gray-300 px-4 py-2 rounded" onclick="document.getElementById('modalEditRealisasi{{ $t->realisasi->id }}').classList.add('hidden')">Batal</button>
          <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">Update Realisasi</button>
        </div>
      </form>
    </div>
  </div>
  @endif
  @empty
  <div class="bg-blue-100 text-blue-700 p-4 rounded">Tidak ada tugas yang tersedia saat ini.</div>
  @endforelse
</div>

<!-- Footer -->
<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>
@endsection