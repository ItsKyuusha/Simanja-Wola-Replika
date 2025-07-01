@extends('layouts.app')
@section('title', 'Master Jenis Tim')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="background-color: #1565c0; color: white;">
            <strong>Tabel Jenis Tim</strong>
        </div>
    <div class="card-body">
    {{-- ALERT --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form Pencarian -->
    <div class="d-flex mb-3">
        <form method="GET" action="{{ route('superadmin.masteruser') }}" class="d-flex w-100">
            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari pekerjaan..." aria-label="Cari Pegawai">
            <button class="btn btn-primary" type="submit">Cari</button>
        </form>

        <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#modalUser">+Tambah</button>
    </div>
    <table class="table table-bordered small">
        <thead style="text-align:center">
            <tr>
                <th>No</th>
                <th>Nama Jenis Tim</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tims as $i => $item)
                <tr>
                    <td style="text-align:center">{{ $i + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td style="text-align:center">
                        <button
                            class="btn btn-sm btn-warning btn-edit"
                            data-id="{{ $item->id }}"
                            data-nama="{{ $item->nama }}"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditTim"
                        >
                            Edit
                        </button>

                        <form action="{{ route('superadmin.jenis-tim.destroy', $item->id) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin hapus?')" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalUser" tabindex="-1" aria-labelledby="modalUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUserLabel">Tambah Jenis Tim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('superadmin.jenis-tim.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Jenis Tim</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEditTim" tabindex="-1" aria-labelledby="modalEditTimLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formEditTim">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditTimLabel">Edit Jenis Tim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-nama" class="form-label">Nama Jenis Tim</label>
                        <input type="text" class="form-control" id="edit-nama" name="nama" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- SCRIPT UNTUK HANDLE EDIT --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-edit');
        const formEdit = document.getElementById('formEditTim');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const nama = this.dataset.nama;

                formEdit.action = `/superadmin/jenis-tim/${id}`; // Pastikan route ini sesuai

                document.getElementById('edit-nama').value = nama;
            });
        });
    });
</script>
@endpush

@endsection
