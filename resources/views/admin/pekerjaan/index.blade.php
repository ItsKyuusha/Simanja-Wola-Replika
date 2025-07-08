@extends('layouts.app')
@section('title', 'Daftar Tugas Tim')

@section('content')
<div class="container">
  <h1 class="mb-4">Daftar Tugas Tim</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Tugas</button>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>No.</th>
        <th>Nama Tugas</th>
        <th>Pegawai</th>
        <th>Jenis</th>
        <th>Target</th>
        <th>Deadline</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($tugas as $t)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $t->nama_tugas }}</td>
        <td>{{ $t->pegawai->nama }}</td>
        <td>{{ $t->jenisPekerjaan->nama_pekerjaan }}</td>
        <td>{{ $t->target }} {{ $t->satuan }}</td>
        <td>{{ \Carbon\Carbon::parse($t->deadline)->format('d M Y') }}</td>
        <td>
          @if (!$t->realisasi)
            Belum Dikerjakan
          @elseif ($t->realisasi->realisasi < $t->target)
            Ongoing
          @else
            Selesai Dikerjakan
          @endif
        </td>
        <td>
          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $t->id }}">Edit</button>
          <form action="{{ route('admin.pekerjaan.destroy', $t->id) }}" method="POST" class="d-inline">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</button>
          </form>
        </td>
      </tr>

      <!-- Modal Edit -->
      <div class="modal fade" id="editModal{{ $t->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $t->id }}" aria-hidden="true">
        <div class="modal-dialog">
          <form method="POST" action="{{ route('admin.pekerjaan.update', $t->id) }}">
            @csrf @method('PUT')
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $t->id }}">Edit Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">

                <div class="mb-3">
                  <label>Pegawai</label>
                  <select name="pegawai_id" class="form-control form-select" required>
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach($pegawai as $p)
                    <option value="{{ $p->id }}" {{ $t->pegawai_id == $p->id ? 'selected' : '' }}>
                        {{ $p->nama }} - {{ $p->jabatan }}
                    </option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-3">
                  <label>Jenis Pekerjaan</label>
                  <select name="jenis_pekerjaan_id" class="form-control form-select" required>
                    <option value="">-- Pilih Jenis --</option>
                    @foreach($jenisPekerjaan as $j)
                      <option value="{{ $j->id }}" {{ $t->jenis_pekerjaan_id == $j->id ? 'selected' : '' }}>{{ $j->nama_pekerjaan }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-3">
                  <label>Nama Tugas</label>
                  <input type="text" name="nama_tugas" class="form-control" value="{{ $t->nama_tugas }}" required>
                </div>

                <div class="mb-3">
                  <label>Target</label>
                  <input type="number" name="target" class="form-control" value="{{ $t->target }}" required>
                </div>

                <div class="mb-3">
                  <label>Satuan</label>
                  <input type="text" name="satuan" class="form-control" value="{{ $t->satuan }}" required>
                </div>

                <div class="mb-3">
                  <label>Asal Instruksi</label>
                  <input type="text" name="asal" class="form-control" value="{{ $t->asal }}">
                </div>

                <div class="mb-3">
                  <label>Deadline</label>
                  <input type="date" name="deadline" class="form-control" value="{{ \Carbon\Carbon::parse($t->deadline)->format('Y-m-d') }}" required>
                </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Update</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      @empty
  <tr>
    <td colspan="8" class="text-center">Belum ada tugas yang ditambahkan.</td>
  </tr>
  @endforelse
    </tbody>
  </table>

  <!-- Modal Tambah -->
  <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="{{ route('admin.pekerjaan.store') }}">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="tambahModalLabel">Tambah Tugas</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">

            <div class="mb-3">
              <label>Pegawai</label>
              <select name="pegawai_id" class="form-control form-select" required>
                <option value="">-- Pilih Pegawai --</option>
                @foreach($pegawai as $p)
                  <option value="{{ $p->id }}">{{ $p->nama }} - {{ $p->jabatan }}</option>
                @endforeach
              </select>
            </div>

            <div class="mb-3">
              <label>Jenis Pekerjaan</label>
              <select name="jenis_pekerjaan_id" class="form-control form-select" required>
                <option value="">-- Pilih Jenis --</option>
                @foreach($jenisPekerjaan as $j)
                  <option value="{{ $j->id }}">{{ $j->nama_pekerjaan }}</option>
                @endforeach
              </select>
            </div>

            <div class="mb-3">
              <label>Nama Tugas</label>
              <input type="text" name="nama_tugas" class="form-control" required>
            </div>

            <div class="mb-3">
              <label>Target</label>
              <input type="number" name="target" class="form-control" required>
            </div>

            <div class="mb-3">
              <label>Satuan</label>
              <input type="text" name="satuan" class="form-control" required>
            </div>

            <div class="mb-3">
              <label>Asal Instruksi</label>
              <input type="text" name="asal" class="form-control">
            </div>

            <div class="mb-3">
              <label>Deadline</label>
              <input type="date" name="deadline" class="form-control" required>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
