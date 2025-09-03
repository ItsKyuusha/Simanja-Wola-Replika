@extends('layouts.app')
@section('page-title', 'Pekerjaan')

@section('content')
<style>
  [x-cloak] {
    display: none !important;
  }
</style>

<div x-data="{ tambahModal: false, editModal: null, openImport: false }">

  <div class="bg-white rounded-2xl p-6 mb-12 border border-gray-200">

    {{-- Judul dan Tombol Tambah --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 flex-wrap">
      <h2 class="text-2xl font-semibold text-blue-600">Daftar Tugas Tim</h2>

      <div class="flex flex-col sm:flex-row items-center gap-3">
        <!-- Form Search -->
        <form method="GET" action="{{ route('admin.pekerjaan.index') }}" class="flex gap-3 w-full sm:w-auto">
          <input type="text" name="search" value="{{ request('search') }}"
            class="px-4 py-2 w-full sm:w-64 border border-gray-300 rounded-lg 
                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                   bg-white/50 backdrop-blur-sm placeholder-gray-500"
            placeholder="Cari nama pegawai...">
          <button type="submit"
            class="px-4 py-2 rounded-lg border border-gray-400 text-gray-600 font-medium 
                   bg-white/40 backdrop-blur-sm hover:bg-gray-100 hover:text-gray-700
                   transition duration-200 ease-in-out transform hover:scale-105">
            <i class="fas fa-search mr-1"></i> Cari
          </button>
        </form>

        <!-- Tombol Export -->
        <a href="{{ route('admin.pekerjaan.export') }}"
          class="inline-flex items-center px-4 py-2 rounded-lg border border-green-400 text-green-600 font-medium
           bg-green-200/20 backdrop-blur-sm shadow-sm 
           hover:bg-green-300/30 hover:border-green-500 hover:text-green-700
           transition duration-200 ease-in-out transform hover:scale-105">
          <i class="fas fa-file-excel mr-2"></i> Export Tabel
        </a>

        <!-- Tombol Import -->
        <button @click="openImport = true"
          class="inline-flex items-center px-4 py-2 rounded-lg border border-purple-400 text-purple-600 font-medium
           bg-purple-200/20 backdrop-blur-sm shadow-sm 
           hover:bg-purple-300/30 hover:border-purple-500 hover:text-purple-700
           transition duration-200 ease-in-out transform hover:scale-105">
          <i class="fas fa-file-upload mr-2"></i> Upload Data
        </button>

        <!--Tambah Tugas-->
        <button @click="tambahModal = true"
          class="inline-flex items-center px-4 py-2 rounded-lg border border-blue-500 text-blue-600 font-medium
                 bg-blue-200/20 backdrop-blur-sm shadow-sm 
                 hover:bg-blue-300/30 hover:border-blue-600 hover:text-blue-700
                 transition duration-200 ease-in-out transform hover:scale-105">
          <i class="fas fa-clipboard-list mr-2"></i> Tambah Tugas
        </button>
      </div>
    </div>

    {{-- Pesan Sukses --}}
    @if(session('success'))
    <div class="mb-4 bg-green-50 text-green-700 px-4 py-2 rounded-md border border-green-200">
      {{ session('success') }}
    </div>
    @endif

    {{-- Table --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
      <table class="w-full table-auto text-sm text-gray-700">
        <thead class="bg-gradient-to-r from-blue-100 to-blue-200 text-center text-sm text-gray-700">
          <tr>
            <th class="p-3 border">No.</th>
            <th class="p-3 border">Nama Tugas</th>
            <th class="p-3 border">Pegawai</th>
            <th class="p-3 border">Jenis</th>
            <th class="p-3 border">Target</th>
            <th class="p-3 border">Deadline</th>
            <th class="p-3 border">Status</th>
            <th class="p-3 border">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tugas as $t)
          <tr class="text-center hover:bg-gray-50">
            <td class="p-3 border">{{ $loop->iteration }}</td>
            <td class="p-3 border">{{ $t->nama_tugas }}</td>
            <td class="p-3 border">{{ $t->pegawai->nama }}</td>
            <td class="p-3 border">{{ $t->jenisPekerjaan->nama_pekerjaan }}</td>
            <td class="p-3 border">{{ $t->target }} {{ $t->satuan }}</td>
            <td class="p-3 border">{{ \Carbon\Carbon::parse($t->deadline)->format('d M Y') }}</td>
            <td class="p-3 border">
              @if (!$t->realisasi)
              <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded">
                Belum Dikerjakan
              </span>
              @elseif ($t->realisasi->realisasi < $t->target)
                <span class="inline-block px-2 py-1 text-xs font-semibold text-black bg-yellow-300 rounded">
                  Ongoing
                </span>
                @else
                <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-green-600 rounded">
                  Selesai Dikerjakan
                </span>
                @endif
            </td>
            <td class="p-3 border">
              <button @click="editModal = {{ $t->id }}"
                class="px-3 py-1 rounded-lg border border-yellow-400 text-yellow-600 bg-yellow-100/40 backdrop-blur-sm text-xs
                     hover:bg-yellow-200 hover:text-yellow-700 transition">
                <i class="fas fa-edit mr-1"></i> Edit
              </button>
              <form action="{{ route('admin.pekerjaan.destroy', $t->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button onclick="return confirm('Yakin hapus?')"
                  class="px-3 py-1 rounded-lg border border-red-500 text-red-600 bg-red-100/40 backdrop-blur-sm text-xs
                       hover:bg-red-200 hover:text-red-700 transition">
                  <i class="fas fa-trash mr-1"></i> Hapus
                </button>
              </form>
            </td>
          </tr>

          {{-- Modal Edit --}}
          <div x-show="editModal === {{ $t->id }}" x-cloak
            x-transition.opacity.duration.300ms
            class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50">
            <div @click.away="editModal = null"
              x-transition.scale.origin.center.duration.300ms
              class="bg-white rounded-lg w-full max-w-lg p-6 shadow-lg">
              <h2 class="text-lg font-semibold mb-4">Edit Tugas</h2>
              <form method="POST" action="{{ route('admin.pekerjaan.update', $t->id) }}">
                @csrf
                @method('PUT')
                <div class="space-y-4">
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
                  <div>
                    <label class="block mb-1">Jenis Pekerjaan</label>
                    <select name="jenis_pekerjaan_id" id="jenisEdit{{ $t->id }}"
                      class="w-full border rounded px-3 py-2" required>
                      <option value="">-- Pilih Jenis --</option>
                      @foreach($jenisPekerjaan as $j)
                      <option value="{{ $j->id }}" data-satuan="{{ $j->satuan }}"
                        {{ $t->jenis_pekerjaan_id == $j->id ? 'selected' : '' }}>
                        {{ $j->nama_pekerjaan }}
                      </option>
                      @endforeach
                    </select>
                  </div>
                  <div>
                    <label class="block mb-1">Nama Tugas</label>
                    <input type="text" name="nama_tugas" class="w-full border rounded px-3 py-2"
                      value="{{ $t->nama_tugas }}" required>
                  </div>
                  <div>
                    <label class="block mb-1">Target</label>
                    <input type="number" name="target" class="w-full border rounded px-3 py-2"
                      value="{{ $t->target }}" required>
                  </div>
                  <div>
                    <label class="block mb-1">Satuan</label>
                    <input type="text" name="satuan" id="satuanInput{{ $t->id }}"
                      class="w-full border rounded px-3 py-2"
                      value="{{ $t->satuan }}" readonly required>
                  </div>
                  <div>
                    <label class="block mb-1">Asal Instruksi</label>
                    <input type="text" name="asal" class="w-full border rounded px-3 py-2"
                      value="{{ auth()->user()->pegawai->team->nama ?? '' }}" readonly>
                  </div>
                  <div>
                    <label class="block mb-1">Deadline</label>
                    <input type="date" name="deadline" class="w-full border rounded px-3 py-2"
                      value="{{ \Carbon\Carbon::parse($t->deadline)->format('Y-m-d') }}" required>
                  </div>
                </div>
                <div class="mt-4 text-right">
                  <button type="button" @click="editModal = null"
                    class="mr-2 px-4 py-2 border rounded">Batal</button>
                  <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Update</button>
                </div>
              </form>
            </div>
          </div>
          @empty
          <tr>
            <td colspan="8" class="text-center border px-4 py-6 text-gray-500">
              Belum ada tugas yang ditambahkan.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>


  <!-- Modal Import -->
  <template x-if="openImport">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="bg-white/90 backdrop-blur-md p-6 rounded-2xl w-full max-w-md relative border border-gray-200 shadow-xl">
        <button @click="openImport = false"
          class="absolute top-3 right-4 text-gray-400 text-2xl hover:text-red-500">&times;</button>
        <h2 class="text-lg font-semibold mb-4 text-gray-700">Import Data User & Pegawai</h2>
        <form action="{{ route('admin.pekerjaan.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="file" name="file" accept=".xlsx,.xls"
            class="border rounded-lg w-full px-3 py-2 mb-4" required>
          <div class="flex justify-end gap-2">
            <button type="button" @click="openImport = false"
              class="px-4 py-2 rounded-lg border border-gray-400 bg-gray-100/60 text-gray-700 hover:bg-gray-200 transition">
              Batal
            </button>
            <button type="submit"
              class="px-4 py-2 rounded-lg border border-purple-500 bg-purple-100/60 text-purple-700 hover:bg-purple-200 transition">
              Upload
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>

  {{-- Modal Tambah --}}
  <div x-show="tambahModal" x-cloak
    x-transition.opacity.duration.300ms
    class="fixed inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm z-50">
    <div @click.away="tambahModal = false"
      x-transition.scale.origin.center.duration.300ms
      class="bg-white rounded-lg w-full max-w-lg p-6 shadow-lg">
      <h2 class="text-lg font-semibold mb-4">Tambah Tugas</h2>
      <form method="POST" action="{{ route('admin.pekerjaan.store') }}">
        @csrf
        <div class="space-y-4">
          <div>
            <label class="block mb-1">Pegawai</label>
            <select name="pegawai_id" class="w-full border rounded px-3 py-2" required>
              <option value="">-- Pilih Pegawai --</option>
              @foreach($pegawai as $p)
              <option value="{{ $p->id }}">{{ $p->nama }} - {{ $p->jabatan }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block mb-1">Jenis Pekerjaan</label>
            <select name="jenis_pekerjaan_id" id="jenisTambah"
              class="w-full border rounded px-3 py-2" required>
              <option value="">-- Pilih Jenis --</option>
              @foreach($jenisPekerjaan as $j)
              <option value="{{ $j->id }}" data-satuan="{{ $j->satuan }}">
                {{ $j->nama_pekerjaan }}
              </option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block mb-1">Nama Tugas</label>
            <input type="text" name="nama_tugas" class="w-full border rounded px-3 py-2" required>
          </div>
          <div>
            <label class="block mb-1">Target</label>
            <input type="number" name="target" class="w-full border rounded px-3 py-2" required>
          </div>
          <div>
            <label class="block mb-1">Satuan</label>
            <input type="text" name="satuan" id="satuanTambah"
              class="w-full border rounded px-3 py-2" readonly required>
          </div>
          <div>
            <label class="block mb-1">Asal</label>
            <input type="text" name="asal" class="w-full border rounded px-3 py-2"
              value="{{ auth()->user()->pegawai->team->nama_tim ?? '' }}" readonly>
          </div>
          <div>
            <label class="block mb-1">Deadline</label>
            <input type="date" name="deadline" class="w-full border rounded px-3 py-2" required>
          </div>
        </div>
        <div class="mt-4 text-right">
          <button type="button" @click="tambahModal = false" class="mr-2 px-4 py-2 border rounded">
            Batal
          </button>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>

<script>
  document.getElementById('jenisTambah')?.addEventListener('change', function() {
    const satuan = this.options[this.selectedIndex].getAttribute('data-satuan');
    document.getElementById('satuanTambah').value = satuan ?? '';
  });

  @foreach($tugas as $t)
  document.getElementById('jenisEdit{{ $t->id }}')?.addEventListener('change', function() {
    const satuan = this.options[this.selectedIndex].getAttribute('data-satuan');
    document.getElementById('satuanInput{{ $t->id }}').value = satuan ?? '';
  });
  @endforeach
</script>
@endsection