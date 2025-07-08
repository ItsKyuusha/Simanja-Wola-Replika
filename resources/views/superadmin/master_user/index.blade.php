@extends('layouts.app')
@section('title', 'Manajemen User & Pegawai')

@section('content')
<div class="container">
  <h1 class="mb-4">Tabel Akun Pegawai</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">Tambah User</button>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>No.</th>
        <th>Nama Pegawai</th>
        <th>NIP</th>
        <th>Tim</th>
        <th>Jabatan</th>
        <th>Email</th>
        <th>Role</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    @forelse($users as $user)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $user->pegawai->nama ?? '-' }}</td>
        <td>{{ $user->pegawai->nip ?? '-' }}</td>
        <td>{{ $user->pegawai->team->nama_tim ?? '-' }}</td>
        <td>{{ $user->pegawai->jabatan ?? '-' }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ ucfirst($user->role) }}</td>
        <td>
        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $user->id }}">Edit</button>
        <form action="{{ route('superadmin.master_user.destroy', $user->id) }}" method="POST" class="d-inline">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus user ini?')">Hapus</button>
        </form>
        </td>
    </tr>

      <!-- Edit Modal -->
      <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog">
          <form action="{{ route('superadmin.master_user.update', $user->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Edit User & Pegawai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-2">
                  <label>Nama Pegawai</label>
                  <input type="text" name="nama" class="form-control" value="{{ $user->pegawai->nama }}" required>
                </div>
                <div class="mb-2">
                  <label>NIP</label>
                  <input type="text" name="nip" class="form-control" value="{{ $user->pegawai->nip }}" required>
                </div>
                <div class="mb-2">
                  <label>Jabatan</label>
                  <input type="text" name="jabatan" class="form-control" value="{{ $user->pegawai->jabatan }}" required>
                </div>
                <div class="mb-2">
                  <label>Tim</label>
                  <select name="team_id" class="form-control" required>
                    @foreach($teams as $team)
                      <option value="{{ $team->id }}" {{ $user->pegawai->team_id == $team->id ? 'selected' : '' }}>
                        {{ $team->nama_tim }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <hr>
                <div class="mb-2">
                  <label>Nama User</label>
                  <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>
                <div class="mb-2">
                  <label>Email</label>
                  <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>
                <div class="mb-2">
                  <label>Password (biarkan kosong jika tidak diubah)</label>
                  <input type="password" name="password" class="form-control">
                </div>
                <div class="mb-2">
                  <label>Role</label>
                  <select name="role" class="form-control" required>
                    <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
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
        <td colspan="8" class="text-center">Tidak ada data user atau pegawai yang tersedia.</td>
        </tr>
    @endforelse
    </tbody>
  </table>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('superadmin.master_user.store') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah User & Pegawai</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label>Nama Pegawai</label>
            <input type="text" name="nama" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>NIP</label>
            <input type="text" name="nip" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Jabatan</label>
            <input type="text" name="jabatan" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Tim</label>
            <select name="team_id" class="form-control" required>
              @foreach($teams as $team)
                <option value="{{ $team->id }}">{{ $team->nama_tim }}</option>
              @endforeach
            </select>
          </div>
          <hr>
          <div class="mb-2">
            <label>Nama User</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Role</label>
            <select name="role" class="form-control" required>
              <option value="superadmin">Superadmin</option>
              <option value="admin">Admin</option>
              <option value="user">User</option>
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
