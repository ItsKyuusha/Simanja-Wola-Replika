@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 fw-bold">Dashboard Admin</h1>
            <p class="text-muted">Selamat datang di halaman utama admin.</p>
        </div>
    </div>

    <!-- Card Box Section -->
    <div class="row g-4">
        <!-- Card Project -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background-color: #1565c0; color: white;">
                <div class="card-body">
                    <h5 class="card-title">Total Project</h5>
                    <p class="card-text fs-4 fw-semibold">{{ $totalProyek }} Proyek</p>
                </div>
            </div>
        </div>

        <!-- Card Team -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background-color: #1565c0; color: white;">
                <div class="card-body">
                    <h5 class="card-title">Total Tim</h5>
                    <p class="card-text fs-4 fw-semibold">{{ $totalTim }} Tim</p>
                </div>
            </div>
        </div>

        <!-- Card Produktivitas -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background-color: #1565c0; color: white;">
                <div class="card-body">
                    <h5 class="card-title">Produktivitas</h5>
                    <p class="card-text fs-4 fw-semibold">{{ $totalProduktivitas }} Bobot</p>
                </div>
            </div>
        </div>

        <!-- Card Most Active -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0" style="background-color: #1565c0; color: white;">
                <div class="card-body">
                    <h5 class="card-title">Most Active</h5>
                    <p class="card-text fs-4 fw-semibold">
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
</div>
@endsection
