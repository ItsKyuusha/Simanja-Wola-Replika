@extends('layouts.app')

@section('title', 'Master Pegawai')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header" style="background-color: #1565c0; color: white;">
            <strong>Tabel Pegawai BPS Kota Semarang</strong>
        </div>
    <div class="card-body">
    <!-- Form Pencarian -->
    <form method="GET" action="{{ route('admin.masterpegawai') }}" class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari pegawai..." aria-label="Cari Pegawai">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>

    <!-- Tabel responsif dengan Bootstrap -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark" style="text-align:center">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jabatan</th>
                    <th>Tim Kerja</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pegawai as $index => $item)
                    <tr>
                        <td style="text-align:center">{{ $index + 1 }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->nip }}</td>
                        <td>{{ $item->jabatan }}</td>
                        <td>{{ $item->tim->nama ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data pegawai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection
