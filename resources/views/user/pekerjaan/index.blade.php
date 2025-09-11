@extends('layouts.app')
@section('page-title', 'Tugas Saya')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
  <h3 class="text-lg font-semibold mb-4">Daftar Tugas Anda</h3>

  {{-- Form Search --}}
  <form method="GET" action="{{ route('user.pekerjaan.index') }}" class="flex flex-wrap gap-4 mb-6 items-center max-w-full">
    <input type="text" name="search" value="{{ request('search') }}"
      placeholder="Cari Nama Tugas..."
      class="flex-grow min-w-[150px] max-w-xs px-3 py-2 border rounded" />

    <input type="text" name="jenis_pekerjaan" value="{{ request('jenis_pekerjaan') }}"
      placeholder="Cari Jenis Pekerjaan..."
      class="flex-grow min-w-[150px] max-w-xs px-3 py-2 border rounded" />

    <div class="flex items-center gap-2 min-w-[180px]">
      <label for="deadline_start" class="font-semibold text-gray-700 whitespace-nowrap">Deadline Mulai:</label>
      <input type="date" id="deadline_start" name="deadline_start" value="{{ request('deadline_start') }}"
        class="flex-grow px-3 py-2 border rounded" />
    </div>

    <div class="flex items-center gap-2 min-w-[180px]">
      <label for="deadline_end" class="font-semibold text-gray-700 whitespace-nowrap">Deadline Akhir:</label>
      <input type="date" id="deadline_end" name="deadline_end" value="{{ request('deadline_end') }}"
        class="flex-grow px-3 py-2 border rounded" />
    </div>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded px-4 py-2 whitespace-nowrap">
      Cari
    </button>
  </form>

  @forelse($tugas as $t)
  <div class="border rounded-lg mb-4 overflow-hidden">
    <div class="bg-blue-500 text-white px-4 py-3 flex justify-between items-center">
      <span class="font-semibold">{{ $t->nama_tugas }} - {{ $t->jenisPekerjaan->nama_pekerjaan ?? '-' }}</span>
      <div class="flex items-center gap-2">
        @if($t->total_realisasi == 0)
        <span class="bg-gray-300 text-gray-800 text-xs px-2 py-1 rounded">Belum Dikerjakan</span>
        <button class="bg-white text-gray-800 px-3 py-1 rounded text-sm" onclick="document.getElementById('modalRealisasi{{ $t->id }}').classList.remove('hidden')">Isi Realisasi</button>
        @elseif($t->total_realisasi < $t->target)
          <span class="bg-yellow-300 text-yellow-900 text-xs px-2 py-1 rounded">Ongoing</span>
          <button class="bg-yellow-500 text-white px-3 py-1 rounded text-sm" onclick="document.getElementById('modalRealisasi{{ $t->id }}').classList.remove('hidden')">Tambah Realisasi</button>
          @else
          <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">Selesai</span>
          @endif
      </div>
    </div>

    <div class="p-4">
      <p><strong>Target:</strong> {{ $t->target }} {{ $t->satuan }}</p>
      <p><strong>Total Realisasi:</strong> {{ $t->total_realisasi ?? 0 }}</p>
      <p><strong>Kuantitas:</strong> {{ $t->kuantitas ?? 0 }}%</p>
      <p><strong>Kualitas:</strong> {{ $t->kualitas ?? 0 }}%</p>
      <p>
        <strong>Deadline:</strong> {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y') }}
        @if($t->is_late)
        <span class="ml-2 bg-red-500 text-white px-2 py-1 rounded text-xs">Terlambat</span>
        @else
        <span class="ml-2 bg-green-500 text-white px-2 py-1 rounded text-xs">Tepat Waktu</span>
        @endif
      </p>

      {{-- Rincian histori --}}
      @if($t->rincian && count($t->rincian))
      <div class="mt-4">
        <h5 class="font-semibold mb-2">Histori Realisasi</h5>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm border">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-2 py-1 border">#</th>
                <th class="px-2 py-1 border">Tanggal Input</th>
                <th class="px-2 py-1 border">Tanggal Realisasi</th>
                <th class="px-2 py-1 border">Jumlah</th>
                <th class="px-2 py-1 border">Akumulasi</th>
                <th class="px-2 py-1 border">Capaian</th>
                <th class="px-2 py-1 border">Catatan</th>
                <th class="px-2 py-1 border">Bukti</th>
              </tr>
            </thead>
            <tbody>
              @foreach($t->rincian as $i => $r)
              <tr>
                <td class="px-2 py-1 border text-center">{{ $i+1 }}</td>
                <td class="px-2 py-1 border">{{ \Carbon\Carbon::parse($r['tanggal_input'])->format('d M Y H:i') }}</td>
                <td class="px-2 py-1 border">{{ \Carbon\Carbon::parse($r['tanggal_realisasi'])->format('d M Y') }}</td>
                <td class="px-2 py-1 border text-center">{{ $r['jumlah'] }}</td>
                <td class="px-2 py-1 border text-center">{{ $r['akumulasi'] }}</td>
                <td class="px-2 py-1 border text-center">{{ $r['persen'] }}%</td>
                <td class="px-2 py-1 border">{{ $r['catatan'] ?? '-' }}</td>
                <td class="px-2 py-1 border text-center">
                  @if(!empty($r['file_bukti']))
                  <a href="{{ asset('storage/'.$r['file_bukti']) }}" target="_blank" class="text-blue-600 underline">Lihat</a>
                  @else
                  -
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif
    </div>

    {{-- Modal Input Realisasi --}}
    <div id="modalRealisasi{{ $t->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
        <h5 class="text-lg font-semibold mb-4">Realisasi Tugas: {{ $t->nama_tugas }}</h5>

        {{-- Tampilkan error khusus modal ini --}}
        @if($errors->any())
        <div class="bg-red-100 text-red-700 p-2 rounded mb-3 text-sm">
          <ul class="list-disc pl-4">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <form action="{{ route('user.pekerjaan.realisasi', $t->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
          @csrf
          <input type="number" name="realisasi" placeholder="Jumlah Realisasi"
            class="w-full border rounded px-3 py-2"
            min="1" max="{{ $t->target - $t->total_realisasi }}"
            required>
          <input type="date" name="tanggal_realisasi"
            class="w-full border rounded px-3 py-2"
            min="{{ $t->created_at->toDateString() }}"
            max="{{ $t->deadline }}" required>
          <textarea name="catatan" placeholder="Catatan" class="w-full border rounded px-3 py-2"></textarea>
          <input type="file" name="file_bukti" accept=".pdf,image/*" class="w-full border rounded px-3 py-2">
          <div class="flex justify-end gap-2">
            <button type="button" class="bg-gray-300 px-4 py-2 rounded" onclick="document.getElementById('modalRealisasi{{ $t->id }}').classList.add('hidden')">Batal</button>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Kirim</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @empty
  <div class="bg-blue-100 text-blue-700 p-4 rounded">Tidak ada tugas yang tersedia saat ini.</div>
  @endforelse
</div>

{{-- Footer --}}
<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>
@endsection