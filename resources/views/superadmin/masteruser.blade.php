@extends('layouts.app')
@section('title', 'Kelola Akun Pegawai')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="background-color: #1565c0; color: white;">
            <strong>Tabel Akun Pegawai</strong>
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

    <!-- Form Pencarian dan Tambah User dalam Satu Baris -->
    <div class="d-flex mb-3">
        <form method="GET" action="{{ route('superadmin.masteruser') }}" class="d-flex w-100">
            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari pegawai..." aria-label="Cari Pegawai">
            <button class="btn btn-primary" type="submit">Cari</button>
        </form>

        <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#modalUser">+Tambah</button>
    </div>

    <table class="table table-bordered small">
        <thead style="text-align:center">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIP</th>
                <th>Tim Kerja</th>
                <th>Jabatan</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $i => $item)
                <tr>
                    <td style="text-align:center">{{ $i + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->nip }}</td>
                    <td>{{ $item->tim->nama ?? '-' }}</td>
                    <td>{{ $item->jabatan }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ ucfirst($item->role) }}</td>
                    <td style="text-align:center">
                        <button 
                            class="btn btn-sm btn-warning btn-edit" 
                            data-id="{{ $item->id }}"
                            data-nama="{{ $item->nama }}"
                            data-nip="{{ $item->nip }}"
                            data-jabatan="{{ $item->jabatan }}"
                            data-email="{{ $item->email }}"
                            data-tim_id="{{ $item->tim_id }}"
                            data-role="{{ $item->role }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#modalEditUser"
                        >
                            Edit
                        </button>
                        <form action="{{ route('superadmin.masteruser.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin hapus?')">
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
                <h5 class="modal-title" id="modalUserLabel">Tambah Akun Pegawai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('superadmin.masteruser.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="nip" class="form-label">NIP</label>
                        <input type="text" class="form-control" id="nip" name="nip" required>
                    </div>
                    <div class="mb-3">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <input type="text" class="form-control" id="jabatan" name="jabatan" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="tim_id" class="form-label">Tim Kerja</label>
                        <select class="form-select" id="tim_id" name="tim_id" required>
                            <option value="">Pilih Tim</option>
                            @foreach($tims as $team)
                                <option value="{{ $team->id }}">{{ $team->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="superadmin">Superadmin</option>
                            <option value="admin">Admin</option>
                            <!-- Add other roles if necessary -->
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- SCRIPT UNTUK HANDLE EDIT --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-edit');
        const formEdit = document.getElementById('formEditUser');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const nip = this.dataset.nip;
                const jabatan = this.dataset.jabatan;
                const email = this.dataset.email;
                const tim_id = this.dataset.tim_id;
                const role = this.dataset.role;

                formEdit.action = `/superadmin/masteruser/${id}`; // Ubah sesuai route update

                document.getElementById('edit-nama').value = nama;
                document.getElementById('edit-nip').value = nip;
                document.getElementById('edit-jabatan').value = jabatan;
                document.getElementById('edit-email').value = email;
                document.getElementById('edit-tim_id').value = tim_id;
                document.getElementById('edit-role').value = role;
            });
        });
    });
</script>
@endpush

@endsection
