@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 fw-bold">Dashboard Admin</h1>
            <p class="text-muted">Selamat datang di halaman utama admin.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Pegawai</h5>
                    <p class="card-text fs-4 fw-semibold">120</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-success">Progress Aktif</h5>
                    <p class="card-text fs-4 fw-semibold">8 Proyek</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-warning">Pekerjaan Tertunda</h5>
                    <p class="card-text fs-4 fw-semibold">3 Tugas</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
