@extends('layouts.app')
@section('title', 'Progress Tugas')

@section('content')
<div class="bg-white shadow rounded p-6">

  {{-- Judul dan Form Search --}}
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-2xl font-semibold text-gray-700">Rekap Tugas Tim</h3>

    <form method="GET" action="{{ route('admin.progress.index') }}" class="flex items-center gap-2">
      <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        title="Cari berdasarkan nama pegawai atau tugas..."
        class="w-full sm:w-80 border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200"
        placeholder="Cari nama pegawai atau tugas..." />
      <button
        type="submit"
        class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md text-gray-700 border border-gray-300">
        Cari
      </button>
    </form>
  </div>



  {{-- Table --}}
  <div class="overflow-x-auto">
    <table class="min-w-full border border-gray-300 text-sm text-left">
      <thead class="bg-blue-100 text-gray-700 text-center">
        <tr>
          <th class="px-3 py-2 border">No.</th>
          <th class="px-3 py-2 border">Nama Pegawai</th>
          <th class="px-3 py-2 border">Nama Tugas</th>
          <th class="px-3 py-2 border">Target</th>
          <th class="px-3 py-2 border">Realisasi</th>
          <th class="px-3 py-2 border">Kualitas</th>
          <th class="px-3 py-2 border">Kuantitas</th>
          <th class="px-3 py-2 border">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tugas as $t)
        <tr class="text-center hover:bg-gray-50">
          <td class="px-3 py-2 border">{{ $loop->iteration }}</td>
          <td class="px-3 py-2 border">{{ $t->pegawai->nama }}</td>
          <td class="px-3 py-2 border">{{ $t->nama_tugas }}</td>
          <td class="px-3 py-2 border">{{ $t->target }} {{ $t->satuan }}</td>
          <td class="px-3 py-2 border">{{ $t->realisasi->realisasi ?? '-' }}</td>
          <td class="px-3 py-2 border">{{ $t->realisasi->nilai_kualitas ?? '-' }}</td>
          <td class="px-3 py-2 border">{{ $t->realisasi->nilai_kuantitas ?? '-' }}</td>
          <td class="px-3 py-2 border">
            @if (!$t->realisasi)
            <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded">
              Belum Dikerjakan
            </span>
            @elseif ($t->realisasi->realisasi < $t->target)
              <span class="inline-block px-2 py-1 text-xs font-semibold text-black bg-yellow-300 rounded">Ongoing</span>
              @else
              <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-green-600 rounded">Selesai Dikerjakan</span>
              @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center px-3 py-4 border text-gray-500">Tidak ada data tugas.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection