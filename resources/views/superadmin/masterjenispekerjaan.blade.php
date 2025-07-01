@extends('layouts.app')
@section('title', 'Master Jenis Pekerjaan')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="background-color: #1565c0; color: white;">
            <strong>Tabel Jenis Pekerjaan</strong>
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
                <th>Jenis Pekerjaan</th>
                <th>Satuan</th>
                <th>Bobot</th>
                <th>Pemberi Pekerjaan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $item)
                <tr>
                    <td style="text-align:center">{{ $i + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>{{ $item->bobot }}</td>
                    <td>{{ $item->pemberi_pekerjaan }}</td>
                    <td style="text-align:center">
                        <button 
                            class="btn btn-sm btn-warning btn-edit" 
                            data-id="{{ $item->id }}"
                            data-nama="{{ $item->nama }}"
                            data-satuan="{{ $item->satuan }}"
                            data-bobot="{{ $item->bobot }}"
                            data-pemberi="{{ $item->pemberi_pekerjaan }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#modalEditPekerjaan">
                            Edit
                        </button>

                        <form action="{{ route('superadmin.jenis-pekerjaan.destroy', $item->id) }}" method="POST"
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
                <h5 class="modal-title" id="modalUserLabel">Tambah Jenis Pekerjaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('superadmin.jenis-pekerjaan.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama" class="form-label">Jenis Pekerjaan</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="satuan" class="form-label">Satuan</label>
                        <input type="text" class="form-control" id="satuan" name="satuan" required>
                    </div>
                    <div class="mb-3">
                        <label for="bobot" class="form-label">Bobot</label>
                        <input type="number" class="form-control" id="bobot" name="bobot" required>
                    </div>
                    <div class="mb-3">
                        <label for="pemberi_pekerjaan" class="form-label">Pemberi Pekerjaan</label>
                        <input type="text" class="form-control" id="pemberi_pekerjaan" name="pemberi_pekerjaan" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEditPekerjaan" tabindex="-1" aria-labelledby="modalEditPekerjaanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="formEditPekerjaan">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditPekerjaanLabel">Edit Jenis Pekerjaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-nama" class="form-label">Jenis Pekerjaan</label>
                        <input type="text" class="form-control" id="edit-nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-satuan" class="form-label">Satuan</label>
                        <input type="text" class="form-control" id="edit-satuan" name="satuan" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-bobot" class="form-label">Bobot</label>
                        <input type="number" class="form-control" id="edit-bobot" name="bobot" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-pemberi" class="form-label">Pemberi Pekerjaan</label>
                        <input type="text" class="form-control" id="edit-pemberi" name="pemberi_pekerjaan" required>
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
        const formEdit = document.getElementById('formEditPekerjaan');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const satuan = this.dataset.satuan;
                const bobot = this.dataset.bobot;
                const pemberi = this.dataset.pemberi;

                formEdit.action = `/superadmin/jenis-pekerjaan/${id}`; // Pastikan route sesuai

                document.getElementById('edit-nama').value = nama;
                document.getElementById('edit-satuan').value = satuan;
                document.getElementById('edit-bobot').value = bobot;
                document.getElementById('edit-pemberi').value = pemberi;
            });
        });
    });
</script>
@endpush

@endsection
