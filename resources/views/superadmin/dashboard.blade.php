@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 fw-bold">Dashboard Superadmin</h1>
            <p class="text-muted">Selamat datang di halaman utama superadmin.</p>
        </div>
    </div>

    <!-- Card Box Section -->
    <div class="row g-4">
        <!-- Card Project -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background-color: #1565c0; color: white;">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-project-diagram me-2"></i>Total Project</h5>
                    <p class="card-text fs-8 fw-semibold">{{ $totalProyek }} Proyek</p>
                </div>
            </div>
        </div>

        <!-- Card Team -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background-color: #1565c0; color: white;">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users me-2"></i>Total Tim</h5>
                    <p class="card-text fs-8 fw-semibold">{{ $totalTim }} Tim</p>
                </div>
            </div>
        </div>

        <!-- Card Produktivitas -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background-color: #1565c0; color: white;">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-chart-line me-2"></i>Produktivitas</h5>
                    <p class="card-text fs-8 fw-semibold">{{ $totalProduktivitas }} Bobot</p>
                </div>
            </div>
        </div>

        <!-- Card Most Active -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background-color: #1565c0; color: white;">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-trophy me-2"></i>Most Active</h5>
                    <p class="card-text fs-8 fw-semibold">
                        @if ($mostActive)
                            {{ $mostActive->nama }} ({{ $mostActive->jumlah_kegiatan }})
                        @else
                            Tidak ada data.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <br/>

    <!-- Nav Tabs -->
    <div class="d-flex justify-content-center mb-3">
    <div class="nav nav-pills" id="v-pills-tab" role="tablist">
        <a class="nav-link active" id="v-pills-kegiatan-tab" data-bs-toggle="pill" href="#v-pills-kegiatan" role="tab" aria-controls="v-pills-kegiatan" aria-selected="true">Jumlah Kegiatan Pegawai</a>
        <a class="nav-link" id="v-pills-bobot-tab" data-bs-toggle="pill" href="#v-pills-bobot" role="tab" aria-controls="v-pills-bobot" aria-selected="false">Jumlah Bobot Pekerjaan Pegawai</a>
        <a class="nav-link" id="v-pills-kinerja-tab" data-bs-toggle="pill" href="#v-pills-kinerja" role="tab" aria-controls="v-pills-kinerja" aria-selected="false">Nilai Kinerja Pegawai</a>
        <a class="nav-link" id="v-pills-persen-tab" data-bs-toggle="pill" href="#v-pills-persen" role="tab" aria-controls="v-pills-persen" aria-selected="false">Persentase Tugas Selesai Tim</a>
        <a class="nav-link" id="v-pills-persen-tab" data-bs-toggle="pill" href="#v-pills-persen" role="tab" aria-controls="v-pills-persen" aria-selected="false">Performa Tugas</a>
    </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="v-pills-tabContent">
        <!-- Tabel Jumlah Kegiatan -->
        <div class="tab-pane fade show active" id="v-pills-kegiatan" role="tabpanel" aria-labelledby="v-pills-kegiatan-tab">
            <div class="card">
                <div class="card-header" style="background-color: #1565c0; color: white;">
                    <strong>Jumlah Kegiatan Pegawai</strong>
                </div>
                <div class="card-body">
                    <!-- Tabel Pekerjaan -->
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped small">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th>Nama Pegawai</th>
                                    <th>Jumlah kegiatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="2" class="text-center">Tidak ada data pekerjaan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Jumlah Bobot -->
        <div class="tab-pane fade" id="v-pills-bobot" role="tabpanel" aria-labelledby="v-pills-bobot-tab">
            <div class="card">
                <div class="card-header" style="background-color: #1565c0; color: white;">
                    <strong>Jumlah Bobot Pekerjaan Pegawai</strong>
                </div>
                <div class="card-body">
                    <!-- Tabel Pekerjaan -->
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped small">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th>Nama Pegawai</th>
                                    <th>Total Bobot</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="2" class="text-center">Tidak ada data pekerjaan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Nilai Kinerja -->
        <div class="tab-pane fade" id="v-pills-kinerja" role="tabpanel" aria-labelledby="v-pills-kinerja-tab">
            <div class="card">
                <div class="card-header" style="background-color: #1565c0; color: white;">
                    <strong>Nilai Kinerja Pegawai</strong>
                </div>
                <div class="card-body">
                    <!-- Tabel Pekerjaan -->
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped small">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th>Nama Pegawai</th>
                                    <th>Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="2" class="text-center">Tidak ada data pekerjaan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Persentase Tugas Selesai -->
        <div class="tab-pane fade" id="v-pills-persen" role="tabpanel" aria-labelledby="v-pills-persen-tab">
            <div class="card">
                <div class="card-header" style="background-color: #1565c0; color: white;">
                    <strong>Persentase Tugas Selesai Tim</strong>
                </div>
                <div class="card-body">
                    <!-- Tabel Pekerjaan -->
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped small">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th>Nama Pegawai</th>
                                    <th>Persentase Tugas Selesai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="2" class="text-center">Tidak ada data pekerjaan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
