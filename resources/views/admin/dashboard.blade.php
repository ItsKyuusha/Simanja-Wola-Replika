@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<div class="row mb-4">
        <div class="col">
            <h1 class="h3 fw-bold">Dashboard Admin</h1>
            <p class="text-muted">Selamat datang di halaman utama panel admin.</p>
        </div>
    </div>
<!-- Welcome Card -->
<div class="app-card alert alert-dismissible shadow-lg mb-4 border-left-decoration bg-white" role="alert">
    <div class="inner">
        <div class="app-card-body p-3 p-lg-4">
            <div class="row gx-5 gy-3">
                <div class="col-12 col-lg-9">
                    <!-- Teks sambutan dengan lebih bold -->
                    <h3 class="mb-3" style="font-weight: 700; color: #1565c0;">Welcome to Panel Admin!</h3> 
                    <p class="text-justify" style="text-align: justify; font-weight: 600; color: #333;">
                        "WOLA" adalah sebuah platform yang dikembangkan sebagai replika dari Sistem Manajemen Kinerja "Simanja" yang sebelumnya digunakan oleh BPS Kabupaten Klaten, namun disesuaikan secara khusus untuk memenuhi kebutuhan BPS Kota Semarang. Tujuan utama Wolaku adalah untuk membantu BPS Kota Semarang dalam mengelola, memantau, dan meningkatkan kinerja karyawan serta tim secara lebih efektif dan efisien. Platform ini menyediakan berbagai fitur untuk mengukur dan mengevaluasi kinerja individu maupun kelompok, serta mendukung pencapaian tujuan organisasi dengan pendekatan yang lebih terstruktur dan berbasis data. Wolaku juga memastikan proses evaluasi kinerja berjalan secara transparan, objektif, dan dapat diakses dengan mudah oleh semua pihak yang berkepentingan.
                    </p>
                </div><!--//col-->
                <div class="col-12 col-lg-3">
                    <img src="{{ asset('icon_dashboard.png') }}" alt="Dashboard" class="img-fluid">
                </div><!--//col-->
            </div><!--//row-->
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div><!--//app-card-body-->
    </div><!--//inner-->
</div><!--//app-card-->

<div class="row mt-4">

    <!-- Total Tugas Tim Card -->
    <div class="col-md-4">
        <div class="small-box bg-primary shadow-lg rounded-3 card-hover">
            <div class="inner">
                <h3 class="font-weight-bold text-white" style="font-size: 2rem;">{{ $totalTugas }}</h3>
                <p class="text-white" style="font-size: 1rem;">Total Tugas Tim</p>
            </div>
            <div class="icon">
                <i class="fas fa-tasks" style="font-size: 2.5rem;"></i>
            </div>
        </div>
    </div>

    <!-- Jumlah Anggota Tim Card -->
    <div class="col-md-4">
        <div class="small-box bg-success shadow-lg rounded-3 card-hover">
            <div class="inner">
                <h3 class="font-weight-bold text-white" style="font-size: 2rem;">{{ $jumlahPegawai }}</h3>
                <p class="text-white" style="font-size: 1rem;">Jumlah Anggota Tim</p>
            </div>
            <div class="icon">
                <i class="fas fa-users" style="font-size: 2.5rem;"></i>
            </div>
        </div>
    </div>

    <!-- Anggota Teraktif Card -->
    <div class="col-md-4">
        <div class="small-box bg-warning shadow-lg rounded-3 card-hover">
            <div class="inner">
                <h3 class="font-weight-bold text-white" style="font-size: 2rem;">{{ $mostActive->nama ?? '-' }}</h3>
                <p class="text-white" style="font-size: 1rem;">Anggota Teraktif</p>
            </div>
            <div class="icon">
                <i class="fas fa-star" style="font-size: 2.5rem;"></i>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<!-- Custom CSS -->
<style>
    .small-box {
        border-radius: 15px;
        transition: all 0.3s ease;
        padding: 30px 30px 40px 30px;
        position: relative;
        overflow: hidden;
        min-height: 150px;
    }

    .small-box .icon {
        position: absolute;
        top: 20px;
        right: 20px;
        opacity: 0.3;
        font-size: 3rem;
        z-index: 0;
    }

    .small-box .inner {
        position: relative;
        z-index: 1;
    }

    .card-hover:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
    }

    .small-box .inner h3 {
        font-weight: 700;
        font-size: 2rem;
        color: white;
    }

    .small-box .inner p {
        font-size: 1rem;
        color: white;
        opacity: 0.9;
        margin-bottom: 0;
    }
</style>
@endpush
