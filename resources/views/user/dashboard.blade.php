@extends('layouts.app')
@section('title', 'Dashboard User')

@section('content')

<!-- Dashboard Header -->
<div class="row mb-4">
        <div class="col">
            <h1 class="h3 fw-bold">Dashboard User</h1>
            <p class="text-muted">Selamat datang di halaman utama panel user.</p>
        </div>
    </div>

<!-- Welcome Card -->
<div class="app-card alert alert-dismissible shadow-lg mb-4 border-left-decoration bg-white" role="alert">
    <div class="inner">
        <div class="app-card-body p-3 p-lg-4">
            <div class="row gx-5 gy-3">
                <div class="col-12 col-lg-9">
                    <h3 class="mb-3" style="font-weight: 700; color: #1565c0;">Welcome to Panel User!</h3>  
                    <p class="text-justify" style="text-align: justify; font-weight: 600; color: #333;">
                        "WOLA" adalah sebuah platform yang dikembangkan sebagai replika dari Sistem Manajemen Kinerja "Simanja" yang sebelumnya digunakan oleh BPS Kabupaten Klaten, namun disesuaikan secara khusus untuk memenuhi kebutuhan BPS Kota Semarang. Tujuan utama Wolaku adalah untuk membantu BPS Kota Semarang dalam mengelola, memantau, dan meningkatkan kinerja karyawan serta tim secara lebih efektif dan efisien. Platform ini menyediakan berbagai fitur untuk mengukur dan mengevaluasi kinerja individu maupun kelompok, serta mendukung pencapaian tujuan organisasi dengan pendekatan yang lebih terstruktur dan berbasis data. Wolaku juga memastikan proses evaluasi kinerja berjalan secara transparan, objektif, dan dapat diakses dengan mudah oleh semua pihak yang berkepentingan.
                    </p>
                </div>
                <div class="col-12 col-lg-3">
                    <img src="{{ asset('icon_dashboard.png') }}" alt="Dashboard" class="img-fluid">
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Card Count -->
<div class="row mt-4">
    <!-- Total Tugas -->
    <div class="col-md-6 mb-4">
        <div class="small-box bg-primary shadow-lg rounded-3 card-hover position-relative overflow-hidden text-white p-4">
            <div class="inner position-relative z-1">
                <h3 class="fw-bold">{{ $totalTugas }}</h3>
                <p>Total Tugas</p>
            </div>
            <div class="icon position-absolute top-0 end-0 pe-4 pt-3 opacity-25" style="font-size: 3rem;">
                <i class="fas fa-tasks"></i>
            </div>
        </div>
    </div>

    <!-- Total Realisasi -->
    <div class="col-md-6 mb-4">
        <div class="small-box bg-success shadow-lg rounded-3 card-hover position-relative overflow-hidden text-white p-4">
            <div class="inner position-relative z-1">
                <h3 class="fw-bold">{{ $totalRealisasi }}</h3>
                <p>Total Realisasi</p>
            </div>
            <div class="icon position-absolute top-0 end-0 pe-4 pt-3 opacity-25" style="font-size: 3rem;">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .small-box h3 {
        font-size: 2rem;
    }

    .small-box p {
        margin: 0;
        font-size: 1rem;
        opacity: 0.9;
    }

    .small-box .icon {
        z-index: 0;
    }
</style>
@endpush
