<!-- Statistik Cards -->
@extends('layouts.app')
@section('page-title', 'Dashboard Admin')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Grid Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Total Tugas Tim -->
        <div class="bg-white shadow-md rounded-xl p-6 border-l-4 border-blue-600 relative overflow-hidden">
            <div class="flex flex-col space-y-1">
                <h3 class="text-sm font-semibold text-blue-600 uppercase">Total Tugas Tim</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $totalTugas }}</p>
            </div>
            <div class="absolute top-4 right-4 text-blue-100 text-4xl opacity-30">
                <i class="fas fa-tasks"></i>
            </div>
        </div>

        <!-- Jumlah Anggota Tim -->
        <div class="bg-white shadow-md rounded-xl p-6 border-l-4 border-green-600 relative overflow-hidden">
            <div class="flex flex-col space-y-1">
                <h3 class="text-sm font-semibold text-green-600 uppercase">Jumlah Anggota Tim</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $jumlahPegawai }}</p>
            </div>
            <div class="absolute top-4 right-4 text-green-100 text-4xl opacity-30">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <!-- Anggota Teraktif -->
        <div class="bg-white shadow-md rounded-xl p-6 border-l-4 border-yellow-500 relative overflow-hidden">
            <div class="flex flex-col space-y-1">
                <h3 class="text-sm font-semibold text-yellow-600 uppercase">Anggota Teraktif</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $mostActive->nama ?? '-' }}</p>
            </div>
            <div class="absolute top-4 right-4 text-yellow-100 text-4xl opacity-30">
                <i class="fas fa-star"></i>
            </div>
        </div>

    </div>
</div>
@endsection
