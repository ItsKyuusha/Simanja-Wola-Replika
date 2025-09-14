@extends('layouts.app')
@section('page-title', 'Dashboard User')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="{ tab: 'tabel' }">

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow-md rounded-xl p-6 border-l-4 border-blue-600">
            <h3 class="text-sm font-semibold text-blue-600 uppercase">Total Tugas Anda</h3>
            <p class="text-3xl font-bold text-gray-800">{{ $totalTugas }}</p>
        </div>

        <div class="bg-white shadow-md rounded-xl p-6 border-l-4 border-green-600">
            <h3 class="text-sm font-semibold text-green-600 uppercase">Total Bobot</h3>
            <p class="text-3xl font-bold text-gray-800">{{ $totalBobot ?? 0 }}</p>
        </div>

        <div class="bg-white shadow-md rounded-xl p-6 border-l-4 border-yellow-500">
            <h3 class="text-sm font-semibold text-yellow-600 uppercase">Rata-rata Nilai Akhir</h3>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($rincian->avg('nilaiAkhir') ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-6 flex border-b">
        <button
            class="px-4 py-2 text-sm font-medium"
            :class="tab === 'tabel' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
            @click="tab = 'tabel'">
            📋 Rincian Tugas
        </button>
        <button
            class="px-4 py-2 text-sm font-medium ml-4"
            :class="tab === 'grafik' ? 'border-b-2 border-green-600 text-green-600' : 'text-gray-600'"
            @click="tab = 'grafik'">
            📊 Grafik
        </button>
    </div>

    <!-- Tab Content -->
    <div>
        <!-- Rincian Tugas User -->
        <div x-show="tab === 'tabel'" class="bg-white shadow rounded-xl p-6">
            <h2 class="text-lg font-bold mb-4">Rincian Tugas Anda</h2>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2">Nama Pekerjaan</th>
                            <th class="border px-3 py-2">Tim</th>
                            <th class="border px-3 py-2">Target</th>
                            <th class="border px-3 py-2">Bobot</th>
                            <th class="border px-3 py-2">Hari Telat</th>
                            <th class="border px-3 py-2">Nilai Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rincian as $t)
                        <tr>
                            <td class="border px-3 py-2">{{ $t->nama_pekerjaan }}</td>
                            <td class="border px-3 py-2">{{ $t->nama_tim ?? '-' }}</td>
                            <td class="border px-3 py-2">{{ $t->target }}</td>
                            <td class="border px-3 py-2">{{ $t->bobot }}</td>
                            <td class="border px-3 py-2">{{ $t->hariTelat }}</td>
                            <td class="border px-3 py-2">{{ number_format($t->nilaiAkhir, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-gray-500">Belum ada tugas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grafik Target vs Realisasi User -->
        <div x-show="tab === 'grafik'" class="bg-white shadow rounded-xl p-6">
            <h2 class="text-lg font-bold mb-4">Grafik Target vs Realisasi Anda</h2>
            <canvas id="targetChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const ctx = document.getElementById('targetChart').getContext('2d');
        const chartData = @json($rincian);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.map(d => d.nama_pekerjaan),
                datasets: [{
                        label: 'Target',
                        data: chartData.map(d => d.target),
                        backgroundColor: 'rgba(59, 130, 246, 0.7)'
                    },
                    {
                        label: 'Realisasi',
                        data: chartData.map(d => d.realisasi ?? 0),
                        backgroundColor: 'rgba(16, 185, 129, 0.7)'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

<!-- Footer -->
<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
    © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>
@endsection