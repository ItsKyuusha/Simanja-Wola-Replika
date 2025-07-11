@extends('layouts.app')
@section('title', 'Manajemen Jenis Pekerjaan')

@section('content')
<div class="card mb-4 shadow-sm">
  <div class="card-body">
    <h3 class="mb-3">Tabel Jenis Pekerjaan</h3>

    <!-- Form Pencarian & Tombol Tambah -->
    <div class="row mb-3 justify-content-end">
      <div class="col-md-12 col-lg-12 d-flex justify-content-end">
        <form method="GET" action="{{ route('superadmin.jenis-pekerjaan.index') }}" class="d-flex me-2 flex-grow-1">
          <input type="text" name="search" class="form-control" placeholder="Cari nama pekerjaan..." value="{{ request('search') }}">
          <button type="submit" class="btn btn-outline-secondary ms-2">Cari</button>
        </form>
        <button class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createModal">Tambah Jenis Pekerjaan</button>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Tabel Jenis Pekerjaan -->
    <table class="table table-bordered table-sm">
      <thead class="table-primary text-center">
        <tr>
          <th>No.</th>
          <th>Nama</th>
          <th>Satuan</th>
          <th>Bobot</th>
          <th>Pemberi Pekerjaan (Tim)</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $item)
          <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>{{ $item->nama_pekerjaan }}</td>
            <td class="text-center">{{ $item->satuan }}</td>
            <td class="text-center">{{ $item->bobot }}</td>
            <td>{{ $item->team->nama_tim ?? '-' }}</td>
            <td class="text-center">
              <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">Edit</button>
              <form action="{{ route('superadmin.jenis-pekerjaan.destroy', $item->id) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus jenis pekerjaan ini?')">Hapus</button>
              </form>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog">
              <form action="{{ route('superadmin.jenis-pekerjaan.update', $item->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Jenis Pekerjaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-2">
                      <label>Nama Pekerjaan</label>
                      <input name="nama_pekerjaan" class="form-control" value="{{ $item->nama_pekerjaan }}" required>
                    </div>
                    <div class="mb-2">
                      <label>Satuan</label>
                      <input name="satuan" class="form-control" value="{{ $item->satuan }}" required>
                    </div>
                    <div class="mb-2">
                      <label>Bobot</label>
                      <input name="bobot" class="form-control" type="number" step="any" value="{{ $item->bobot }}" required>
                    </div>
                    <div class="mb-2">
                      <label>Pemberi Pekerjaan (Tim)</label>
                      <select name="tim_id" class="form-control" required>
                        <option value="">-- Pilih Tim --</option>
                        @foreach($teams as $team)
                          <option value="{{ $team->id }}" {{ $item->tim_id == $team->id ? 'selected' : '' }}>
                            {{ $team->nama_tim }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-success">Simpan Perubahan</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        @empty
          <tr>
            <td colspan="6" class="text-center">Tidak ada data jenis pekerjaan yang tersedia.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('superadmin.jenis-pekerjaan.store') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Jenis Pekerjaan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label>Nama Pekerjaan</label>
            <input name="nama_pekerjaan" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Satuan</label>
            <input name="satuan" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Bobot</label>
            <input name="bobot" class="form-control" type="number" step="any" required>
          </div>
          <div class="mb-2">
            <label>Pemberi Pekerjaan (Tim)</label>
            <select name="tim_id" class="form-control" required>
              <option value="">-- Pilih Tim --</option>
              @foreach($teams as $team)
                <option value="{{ $team->id }}">{{ $team->nama_tim }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
