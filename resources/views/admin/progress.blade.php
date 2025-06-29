@extends('layouts.app')

@section('title', 'Data Progress dan Nilai Akhir Pegawai')

@section('content')
<div class="container mt-5">
    <div class="card mb-4">
    <div class="card-header" style="background-color: #1565c0; color: white;">
        <strong>Tabel Kinerja Pegawai BPS Kota Semarang</strong>
    </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped small">
                    <thead class="thead-dark" style="text-align:center">
                        <tr>
                            <th>No</th>
                            <th>Nama Pegawai</th>
                            <th>Tugas</th>
                            <th>Bobot</th>
                            <th>Asal</th>
                            <th>Target</th>
                            <th>Realisasi</th>
                            <th>Satuan</th>
                            <th>Deadline</th>
                            <th>Tanggal Realisasi</th>
                            <th>Nilai Kualitas</th>
                            <th>Nilai Kuantitas</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($progress as $index => $item)
                            <tr>
                                <td style="text-align:center">{{ $index + 1 }}</td>
                                <td>{{ $item->user->nama ?? '-' }}</td>
                                <td>{{ $item->tugas }}</td>
                                <td>{{ $item->bobot }}</td>
                                <td>{{ $item->asal }}</td>
                                <td>{{ $item->target }}</td>
                                <td>{{ $item->realisasi }}</td>
                                <td>{{ $item->satuan }}</td>
                                <td>{{ $item->deadline }}</td>
                                <td>{{ $item->tanggal_realisasi }}</td>
                                <td>{{ $item->nilai_kualitas }}</td>
                                <td>{{ $item->nilai_kuantitas }}</td>
                                <td>{{ $item->keterangan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center">Tidak ada data progress.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="background-color: #1565c0; color: white;">
            <strong>Tabel Nilai Akhir Pegawai</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped small">
                    <thead class="thead-dark" style="text-align:center">
                        <tr>
                            <th>No</th>
                            <th>Nama Pegawai</th>
                            <th>NIP</th>
                            <th>Kategori Bobot</th>
                            <th>Total Bobot</th>
                            <th>Nilai Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nilaiAkhir as $index => $item)
                            <tr>
                                <td style="text-align:center">{{ $index + 1 }}</td>
                                <td>{{ $item->user->nama ?? '-' }}</td>
                                <td>{{ $item->user->nip ?? '-' }}</td>
                                <td>{{ $item->kategori_bobot }}</td>
                                <td>{{ $item->total_bobot }}</td>
                                <td>{{ $item->nilai_akhir ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data nilai akhir.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
