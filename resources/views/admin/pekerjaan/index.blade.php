@extends('layouts.app')
@section('title', 'Daftar Tugas Tim')

@section('content')
<div x-data="{ tambahModal: false, editModal: null }">
  <div class="bg-white shadow rounded p-6">
    {{-- Judul dan Tombol Tambah --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
      <h3 class="text-2xl font-semibold text-gray-700">Daftar Tugas Tim</h3>

      <div class="flex flex-col sm:flex-row gap-2 sm:items-center sm:ml-auto">
        <form action="{{ route('admin.pekerjaan.index') }}" method="GET" class="flex gap-2">
          <input type="text" name="search"
            class="w-full sm:w-72 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring focus:ring-blue-200"
            placeholder="Cari tugas, nama, atau NIP..." value="{{ request('search') }}">
          <button type="submit"
            class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md text-gray-700 border border-gray-300">
            Cari
          </button>
        </form>

        <button @click="tambahModal = true"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
          Tambah Tugas
        </button>
      </div>
    </div>



    {{-- Table --}}
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm border border-gray-300">
        <thead class="bg-blue-100 text-gray-700 text-center">
          <tr>
            <th class="border px-3 py-2">No.</th>
            <th class="border px-3 py-2">Nama Tugas</th>
            <th class="border px-3 py-2">Pegawai</th>
            <th class="border px-3 py-2">Jenis</th>
            <th class="border px-3 py-2">Target</th>
            <th class="border px-3 py-2">Deadline</th>
            <th class="border px-3 py-2">Status</th>
            <th class="border px-3 py-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tugas as $t)
          <tr class="text-center hover:bg-gray-50">
            <td class="border px-3 py-2">{{ $loop->iteration }}</td>
            <td class="border px-3 py-2">{{ $t->nama_tugas }}</td>
            <td class="border px-3 py-2">{{ $t->pegawai->nama }}</td>
            <td class="border px-3 py-2">{{ $t->jenisPekerjaan->nama_pekerjaan }}</td>
            <td class="border px-3 py-2">{{ $t->target }} {{ $t->satuan }}</td>
            <td class="border px-3 py-2">{{ \Carbon\Carbon::parse($t->deadline)->format('d M Y') }}</td>
            <td class="border px-3 py-2">
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
            <td class="border px-3 py-2">
              <button @click="editModal = {{ $t->id }}" class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs px-3 py-1 rounded">
                Edit
              </button>
              <form action="{{ route('admin.pekerjaan.destroy', $t->id) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button onclick="return confirm('Yakin hapus?')"
                  class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded mt-1 sm:mt-0">
                  Hapus
                </button>
              </form>
            </td>
          </tr>

          {{-- Modal Edit (Tailwind + AlpineJS) --}}
          <div x-show="editModal === {{ $t->id }}" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg w-full max-w-lg p-6">
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
                    <select name="jenis_pekerjaan_id" id="jenisEdit{{ $t->id }}" class="w-full border rounded px-3 py-2" required>
                      <option value="">-- Pilih Jenis --</option>
                      @foreach($jenisPekerjaan as $j)
                      <option value="{{ $j->id }}" data-satuan="{{ $j->satuan }}" {{ $t->jenis_pekerjaan_id == $j->id ? 'selected' : '' }}>
                        {{ $j->nama_pekerjaan }}
                      </option>
                      @endforeach
                    </select>
                  </div>
                  <div>
                    <label class="block mb-1">Nama Tugas</label>
                    <input type="text" name="nama_tugas" class="w-full border rounded px-3 py-2" value="{{ $t->nama_tugas }}" required>
                  </div>
                  <div>
                    <label class="block mb-1">Target</label>
                    <input type="number" name="target" class="w-full border rounded px-3 py-2" value="{{ $t->target }}" required>
                  </div>
                  <div>
                    <label class="block mb-1">Satuan</label>
                    <input type="text" name="satuan" id="satuanInput{{ $t->id }}" class="w-full border rounded px-3 py-2" value="{{ $t->satuan }}" readonly required>
                  </div>
                  <div>
                    <label class="block mb-1">Asal Instruksi</label>
                    <input type="text" name="asal" class="w-full border rounded px-3 py-2" value="{{ auth()->user()->pegawai->team->nama ?? '' }}" readonly>
                  </div>
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
  </div>

  {{-- Modal Tambah (AlpineJS) --}}
  <div x-show="tambahModal"
    x-cloak
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-lg w-full max-w-lg p-6">
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
              <option value="{{ $j->id }}" data-satuan="{{ $j->satuan }}">{{ $j->nama_pekerjaan }}</option>
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
            <input type="text" name="asal"
              class="w-full border rounded px-3 py-2"
              value="{{ auth()->user()->pegawai->team->nama_tim ?? '' }}" readonly>
          </div>
          <div>
            <label class="block mb-1">Deadline</label>
            <input type="date" name="deadline" class="w-full border rounded px-3 py-2" required>
          </div>
        </div>
        <div class="mt-4 text-right">
          <button type="button"
            @click="tambahModal = false"
            class="mr-2 px-4 py-2 border rounded">
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