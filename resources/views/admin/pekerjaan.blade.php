@extends('layouts.app')

@section('title', 'Data Pekerjaan')

@section('content')
<div class="container mt-5">
    <!-- Card untuk Tabel Pekerjaan -->
    <div class="card">
        <div class="card-header" style="background-color: #1565c0; color: white;">
            <strong>Tabel Pekerjaan</strong>
        </div>
        <div class="card-body">
            <!-- Tabel Pekerjaan -->
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped small">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Pegawai</th>
                            <th>Tim</th>
                            <th>Tugas</th>
                            <th>Bobot</th>
                            <th>Asal</th>
                            <th>Target</th>
                            <th>Realisasi</th>
                            <th>Satuan</th>
                            <th>Deadline</th>
                            <th>Realisasi Tgl</th>
                            <th>Catatan</th>
                            <th>Nilai Kualitas</th>
                            <th>Nilai Kuantitas</th>
                            <th>Keterangan</th>
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pekerjaan as $index => $item)
                            <tr>
                                <td style="text-align:center">{{ $index + 1 }}</td>
                                <td>{{ $item->user->nama ?? '-' }}</td>
                                <td>{{ $item->user->tim->nama ?? '-' }}</td>
                                <td>{{ $item->tugas }}</td>
                                <td>{{ $item->bobot }}</td>
                                <td>{{ $item->asal }}</td>
                                <td>{{ $item->target }}</td>
                                <td>{{ $item->realisasi }}</td>
                                <td>{{ $item->satuan }}</td>
                                <td>{{ $item->deadline }}</td>
                                <td>{{ $item->tanggal_realisasi }}</td>
                                <td>{{ $item->catatan }}</td>
                                <td>{{ $item->nilai_kualitas }}</td>
                                <td>{{ $item->nilai_kuantitas }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td>
                                    @if($item->file)
                                        <a href="{{ asset('storage/' . $item->file) }}" target="_blank" class="btn btn-link">
                                            Lihat File
                                        </a>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="text-center">Tidak ada data pekerjaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
