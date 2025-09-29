@extends('layouts.app')
@section('page-title', 'Pekerjaan')

@section('content')
<style>
  [x-cloak] {
    display: none !important;
  }
</style>

<div x-data="{ tambahModal: false, editModal: null, openImport: false }" class="bg-white rounded-2xl p-6 mb-12 border border-gray-200">

  {{-- Header: Judul & tombol --}}
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 flex-wrap">
    <h2 class="text-2xl font-semibold text-blue-600">Daftar Tugas Tim</h2>

    <div class="flex flex-col sm:flex-row items-center gap-3">
      {{-- Search --}}
      <form method="GET" action="{{ route('admin.pekerjaan.index') }}" class="flex gap-3 w-full sm:w-auto">
        <input type="text" name="search" value="{{ request('search') }}"
          class="px-4 py-2 w-full sm:w-64 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 placeholder-gray-500"
          placeholder="Cari nama pegawai...">
        <button type="submit" class="px-4 py-2 rounded-lg border border-gray-400 text-gray-600 font-medium bg-white/40 hover:bg-gray-100 hover:text-gray-700 transition duration-200 transform hover:scale-105">
          <i class="fas fa-search mr-1"></i> Cari
        </button>
      </form>

      {{-- Export --}}
      <a href="{{ route('admin.pekerjaan.export') }}"
        class="inline-flex items-center px-4 py-2 rounded-lg border border-green-400 text-green-600 font-medium bg-green-200/20 hover:bg-green-300/30 hover:border-green-500 hover:text-green-700 transition duration-200 transform hover:scale-105">
        <i class="fas fa-file-excel mr-2"></i> Export Tabel
      </a>



      {{-- Tambah --}}
      <button @click="tambahModal = true"
        class="inline-flex items-center px-4 py-2 rounded-lg border border-blue-500 text-blue-600 font-medium bg-blue-200/20 hover:bg-blue-300/30 hover:border-blue-600 hover:text-blue-700 transition duration-200 transform hover:scale-105">
        <i class="fas fa-clipboard-list mr-2"></i> Tambah Tugas
      </button>
    </div>
  </div>

  {{-- Pesan sukses --}}
  @if(session('success'))
  <div class="mb-4 bg-green-50 text-green-700 px-4 py-2 rounded-md border border-green-200">
    {{ session('success') }}
  </div>
  @endif

  {{-- Tabel --}}
  <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm bg-white">
    <table class="w-full table-auto text-sm text-gray-700">
      <thead class="bg-gradient-to-r from-blue-100 to-blue-200 text-center text-sm text-gray-700">
        <tr>
          <th class="p-3 border">No.</th>
          <th class="p-3 border">Nama Pekerjaan</th>
          <th class="p-3 border">Pegawai</th>
          <th class="p-3 border">Target</th>
          <th class="p-3 border">Pemberi Pekerjaan</th>
          <th class="p-3 border">Deadline</th>
          <th class="p-3 border">Status</th>
          <th class="p-3 border">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tugas as $t)
        <tr class="text-center hover:bg-gray-50">
          <td class="p-3 border">{{ $loop->iteration }}</td>
          <td class="p-3 border">{{ $t->jenisPekerjaan->nama_pekerjaan }}</td>
          <td class="p-3 border">{{ $t->pegawai->nama }}</td>
          <td class="p-3 border">{{ $t->target }} {{ $t->satuan }}</td>
          <td class="p-3 border">{{ $t->asal ?? '-' }}</td>
          <td class="p-3 border">{{ \Carbon\Carbon::parse($t->deadline)->format('d M Y') }}</td>
          <td class="p-3 border">
            @if (!$t->realisasi)
            <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded">Belum Dikerjakan</span>
            @elseif ($t->realisasi->realisasi < $t->target)
              <span class="inline-block px-2 py-1 text-xs font-semibold text-black bg-yellow-300 rounded">Ongoing</span>
              @else
              <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-green-600 rounded">Selesai Dikerjakan</span>
              @endif
          </td>
          <td class="p-3 border space-x-1">
            @if (!$t->realisasi)
            <form action="{{ route('admin.pekerjaan.destroy', $t->id) }}" method="POST" class="inline">
              @csrf
              @method('DELETE')
              <button onclick="return confirm('Yakin hapus?')" class="px-3 py-1 rounded-lg border border-red-500 text-red-600 bg-red-100/40 hover:bg-red-200 hover:text-red-700 transition text-xs">
                <i class="fas fa-trash mr-1"></i> Hapus
              </button>
            </form>
            @else
            <span class="text-gray-400 text-xs">Tidak bisa dihapus</span>
            @endif
          </td>
        </tr>

        {{-- Modal Edit --}}
        <div x-show="editModal === {{ $t->id }}" x-cloak
          x-transition.opacity.duration.300ms
          class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50">
          <div @click.away="editModal = null"
            x-transition.scale.origin.center.duration.300ms
            class="bg-white rounded-xl w-full max-w-lg p-6 shadow-lg">
            <h2 class="text-lg font-semibold mb-4">Edit Tugas</h2>
            <form method="POST" action="{{ route('admin.pekerjaan.update', $t->id) }}">
              @csrf
              @method('PUT')
              <div class="space-y-4">
                {{-- Pegawai --}}
                <div>
                  <label class="block mb-1">Pegawai</label>
                  <select name="pegawai_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach($pegawai as $p)
                    <option value="{{ $p->id }}" {{ $t->pegawai_id == $p->id ? 'selected' : '' }}>
                      {{ $p->nama }} - {{ $p->jabatan }}
                    </option>
                    @endforeach
                  </select>
                </div>

                {{-- Jenis Pekerjaan --}}
                <div>
                  <label class="block mb-1">Jenis Pekerjaan</label>
                  <select name="jenis_pekerjaan_id" class="w-full border rounded px-3 py-2 jenis-edit" data-id="{{ $t->id }}" required>
                    <option value="">-- Pilih Jenis Pekerjaan --</option>
                    @foreach($jenisPekerjaanModal as $jp)
                    <option value="{{ $jp->id }}" data-satuan="{{ $jp->satuan }}" {{ $t->jenis_pekerjaan_id == $jp->id ? 'selected' : '' }}>
                      {{ $jp->nama_pekerjaan }} ({{ $jp->team->nama_tim ?? '-' }})
                    </option>
                    @endforeach
                  </select>
                </div>

                {{-- Target --}}
                <div>
                  <label class="block mb-1">Target</label>
                  <input type="number" name="target" class="w-full border rounded px-3 py-2" value="{{ $t->target }}" required>
                </div>

                {{-- Satuan --}}
                <div>
                  <label class="block mb-1">Satuan</label>
                  <input type="text" name="satuan" id="satuanInput{{ $t->id }}" class="w-full border rounded px-3 py-2" value="{{ $t->satuan }}" readonly required>
                </div>

                {{-- Pemberi --}}
                <div>
                  <label class="block mb-1">Pemberi Pekerjaan</label>
                  <input type="text" name="asal" class="w-full border rounded px-3 py-2" value="{{ $t->asal ?? auth()->user()->pegawai->nama ?? auth()->user()->name }}" readonly>
                </div>

                {{-- Deadline --}}
                <div>
                  <label class="block mb-1">Deadline</label>
                  <input type="date" name="deadline" class="w-full border rounded px-3 py-2" value="{{ \Carbon\Carbon::parse($t->deadline)->format('Y-m-d') }}" required>
                </div>
              </div>

              <div class="mt-4 text-right">
                <button type="button" @click="editModal = null" class="mr-2 px-4 py-2 border rounded">Batal</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Update</button>
              </div>
            </form>
          </div>
        </div>
        @empty
        <tr>
          <td colspan="8" class="text-center border px-4 py-6 text-gray-500">Belum ada tugas yang ditambahkan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Modal Tambah --}}
  <div x-show="tambahModal" x-cloak
    x-transition.opacity.duration.300ms
    class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50">
    <div @click.away="tambahModal = false"
      x-transition.scale.origin.center.duration.300ms
      class="bg-white rounded-xl w-full max-w-lg p-6 shadow-lg">
      <h2 class="text-lg font-semibold mb-4">Tambah Tugas</h2>
      <form method="POST" action="{{ route('admin.pekerjaan.store') }}">
        @csrf
        <div class="space-y-4">
          {{-- Pegawai --}}
          <div>
            <label class="block mb-1">Pegawai</label>
            <select name="pegawai_id" class="w-full border rounded px-3 py-2" required>
              <option value="">-- Pilih Pegawai --</option>
              @foreach($pegawai as $p)
              <option value="{{ $p->id }}">{{ $p->nama }} - {{ $p->jabatan }}</option>
              @endforeach
            </select>
          </div>

          {{-- Jenis Pekerjaan --}}
<div>
  <label class="block mb-1">Jenis Pekerjaan</label>
  <select name="jenis_pekerjaan_id" id="jenisTambah" class="w-full border rounded px-3 py-2" required>
    <option value="">-- Pilih Jenis Pekerjaan --</option>
    @foreach($jenisPekerjaanModal as $jp)
      <option value="{{ $jp->id }}" data-satuan="{{ $jp->satuan }}" data-volume="{{ $jp->volume }}">
        {{ $jp->nama_pekerjaan }} (Volume: {{ $jp->volume }})
      </option>
    @endforeach
  </select>
</div>


          {{-- Volume --}}
<div>
  <label class="block mb-1">Volume</label>
  <input type="number" name="volume" id="volumeTambah" class="w-full border rounded px-3 py-2" required>
</div>



          {{-- Target --}}
          <div>
            <label class="block mb-1">Target</label>
            <input type="number" name="target" id="targetTambah" class="w-full border rounded px-3 py-2" value="0" required>
          </div>

          {{-- Satuan --}}
          <div>
            <label class="block mb-1">Satuan</label>
            <input type="text" name="satuan" id="satuanTambah" class="w-full border rounded px-3 py-2" readonly required>
          </div>

          {{-- Pemberi --}}
          <div>
            <label class="block mb-1">Pemberi Pekerjaan</label>
            <input type="text" name="asal" class="w-full border rounded px-3 py-2" value="{{ auth()->user()->pegawai->nama ?? auth()->user()->name }}" readonly required>
          </div>

          {{-- Deadline --}}
          <div>
            <label class="block mb-1">Deadline</label>
            <input type="date" name="deadline" class="w-full border rounded px-3 py-2" required>
          </div>
        </div>

        <div class="mt-4 text-right">
          <button type="button" @click="tambahModal = false" class="mr-2 px-4 py-2 border rounded">Batal</button>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<footer class="text-center text-sm text-gray-500 py-4 border-t mt-10">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>

{{-- Script --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Modal Tambah
    const jenisTambah = document.getElementById('jenisTambah');
    const satuanTambah = document.getElementById('satuanTambah');
    const volumeTambah = document.getElementById('volumeTambah');
    const targetTambah = document.getElementById('targetTambah');

    function updateVolumeTambah() {
      const selected = jenisTambah.selectedOptions[0];
      const sisaVolume = parseFloat(selected?.dataset.volume ?? 0);
      const target = parseFloat(targetTambah.value ?? 0);
volumeTambah.value = sisaVolume; // tampilkan volume apa adanya
      satuanTambah.value = selected?.dataset.satuan ?? '';
    }

    jenisTambah?.addEventListener('change', updateVolumeTambah);
    targetTambah?.addEventListener('input', updateVolumeTambah);

    // Modal Edit
    document.querySelectorAll('.jenis-edit').forEach(select => {
      const id = select.dataset.id;
      const satuanInput = document.getElementById('satuanInput' + id);
      select.addEventListener('change', function() {
        const selected = this.selectedOptions[0];
        satuanInput.value = selected?.dataset.satuan ?? '';
      });
    });
  });
</script>

@endsection