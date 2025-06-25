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
<!-- Modal content here... -->

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
