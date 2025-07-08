@extends('layouts.app')
@section('title', 'Jenis Tim')

@section('content')

<h1 class="mb-4">Data Tim</h1>
{{-- TOMBOL TAMBAH --}}
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">Tambah Jenis Tim</button>

{{-- TABLE --}}
<table class="table table-bordered">
  <thead>
    <tr>
      <th>No</th>
      <th>Jenis Tim</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    @forelse($data as $tim)
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td>{{ $tim->nama_tim }}</td>
      <td>
        {{-- Edit --}}
        <button class="btn btn-sm btn-warning"
                data-bs-toggle="modal"
                data-bs-target="#editModal{{ $tim->id }}">Edit</button>

        {{-- Delete --}}
        <form action="{{ route('superadmin.jenis-tim.destroy', $tim->id) }}" method="POST" style="display:inline-block;">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data ini?')">Hapus</button>
        </form>
      </td>
    </tr>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="editModal{{ $tim->id }}" tabindex="-1" aria-labelledby="editLabel{{ $tim->id }}" aria-hidden="true">
      <div class="modal-dialog">
        <form action="{{ route('superadmin.jenis-tim.update', $tim->id) }}" method="POST">
          @csrf @method('PUT')
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editLabel{{ $tim->id }}">Edit Jenis Tim</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="text" name="nama_tim" class="form-control" value="{{ $tim->nama_tim }}" required>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Simpan</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    @empty
    <tr>
      <td colspan="3" class="text-center">Tidak ada data tim yang tersedia.</td>
    </tr>
  @endforelse
  </tbody>
</table>

{{-- MODAL CREATE --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('superadmin.jenis-tim.store') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createLabel">Tambah Jenis Tim</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="nama_tim" class="form-control" placeholder="Nama Jenis Tim" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection
