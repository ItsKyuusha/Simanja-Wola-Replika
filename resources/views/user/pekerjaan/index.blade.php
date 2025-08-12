@extends('layouts.app')
@section('page-title', 'Pekerjaan')

@section('content')
<div class="bg-white rounded-xl p-6 border border-gray-200" x-data="{ openModal: null }">
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-2xl font-semibold mb-4">Daftar Tugas</h3>

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
                <!-- Header Card -->
                <div class="bg-blue-600 text-white px-4 py-2 flex flex-wrap justify-between items-center gap-2">
                    <strong class="text-sm">
                        {{ $t->nama_tugas }} - {{ $t->jenisPekerjaan->nama_pekerjaan }}
                    </strong>

                    <div class="flex items-center gap-2">
                        @if(!$t->realisasi)
                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded">Belum Dikerjakan</span>
                            <button @click="openModal = 'add-{{ $t->id }}'" class="bg-white text-blue-600 text-xs px-3 py-1 rounded hover:bg-blue-100">
                                Isi Realisasi
                            </button>
                        @elseif($t->realisasi->realisasi < $t->target)
                            <span class="bg-yellow-300 text-yellow-900 text-xs px-2 py-1 rounded">Ongoing</span>
                            <button @click="openModal = 'edit-{{ $t->realisasi->id }}'" class="bg-yellow-500 text-white text-xs px-3 py-1 rounded hover:bg-yellow-600">
                                Edit Realisasi
                            </button>
                        @else
                            <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">Selesai</span>
                            <button @click="openModal = 'edit-{{ $t->realisasi->id }}'" class="bg-yellow-500 text-white text-xs px-3 py-1 rounded hover:bg-yellow-600">
                                Edit Realisasi
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Body Card -->
                <div class="p-4 text-sm">
                    <p><strong>Target:</strong> {{ $t->target }} {{ $t->satuan }}</p>
                    <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y') }}</p>

                    @if($t->realisasi)
                        <div class="bg-green-100 border-l-4 border-green-500 p-3 rounded mt-3 text-xs">
                            <strong>Sudah Dikerjakan</strong><br>
                            Realisasi: {{ $t->realisasi->realisasi }}<br>
                            Tanggal: {{ $t->realisasi->tanggal_realisasi }}<br>
                            Kualitas: {{ $t->realisasi->nilai_kualitas }} |
                            Kuantitas: {{ $t->realisasi->nilai_kuantitas }}<br>
                            @if($t->realisasi->file_bukti)
                                <a href="{{ asset('storage/'.$t->realisasi->file_bukti) }}" target="_blank" class="text-blue-600 underline">
                                    Lihat Bukti
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Modal Add --}}
            @php
                if (Route::has('user.pekerjaan.realisasi')) {
                    $storeAction = route('user.pekerjaan.realisasi', $t->id);
                } elseif (Route::has('user.realisasi.store')) {
                    $storeAction = route('user.realisasi.store', $t->id);
                } elseif (Route::has('user.pekerjaan.realisasi.store')) {
                    $storeAction = route('user.pekerjaan.realisasi.store', $t->id);
                } else {
                    $storeAction = url('user/pekerjaan/'.$t->id.'/realisasi');
                }
            @endphp
            @include('partials.modal-add', ['t' => $t, 'storeAction' => $storeAction])

            {{-- Modal Edit --}}
            @if($t->realisasi)
                @php
                    if (Route::has('user.pekerjaan.realisasi.update')) {
                        $updateAction = route('user.pekerjaan.realisasi.update', $t->realisasi->id);
                    } elseif (Route::has('user.realisasi.update')) {
                        $updateAction = route('user.realisasi.update', $t->realisasi->id);
                    } else {
                        $updateAction = url('user/pekerjaan/realisasi/'.$t->realisasi->id);
                    }
                @endphp
                @include('partials.modal-edit', ['t' => $t, 'updateAction' => $updateAction])
            @endif
        @empty
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded text-sm">
                Tidak ada tugas yang tersedia saat ini.
            </div>
        @endforelse
    </div>
</div>
<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>
@endsection
