@extends('layouts.app')
@section('page-title', 'Dashboard User')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow-md rounded-xl p-6 border-l-4 border-blue-600">
            <h3 class="text-sm font-semibold text-blue-600 uppercase">Total Tugas</h3>
            <p class="text-3xl font-bold text-gray-800">{{ $totalTugas }}</p>
        </div>

        <div class="bg-white shadow-md rounded-xl p-6 border-l-4 border-green-600">
            <h3 class="text-sm font-semibold text-green-600 uppercase">Total Realisasi</h3>
            <p class="text-3xl font-bold text-gray-800">{{ $totalRealisasi }}</p>
        </div>

        <div class="bg-white shadow-md rounded-xl p-6 border-l-4 border-yellow-500">
            <h3 class="text-sm font-semibold text-yellow-600 uppercase">Anggota Teraktif</h3>
            @if($palingAktif)
            <p class="text-xl font-bold text-gray-800">{{ $palingAktif['nama'] }}</p>
            <p class="text-sm text-gray-600">
                Realisasi: {{ $palingAktif['total_realisasi'] }} /
                Target: {{ $palingAktif['total_target'] }}
                ({{ $palingAktif['capaian'] }}%)
            </p>
            <span class="text-xs text-gray-400">Skor: {{ number_format($palingAktif['skor'],2) }}</span>
            @else
            <p class="text-gray-500">-</p>
            @endif
        </div>
    </div>

    <!-- Rincian Pegawai -->
    <div class="bg-white shadow rounded-xl p-6 mb-8">
        <h2 class="text-lg font-bold mb-4">Rincian Capaian Pegawai</h2>
        <table class="w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">Nama</th>
                    <th class="border px-3 py-2">Target</th>
                    <th class="border px-3 py-2">Realisasi</th>
                    <th class="border px-3 py-2">Kuantitas</th>
                    <th class="border px-3 py-2">Kualitas</th>
                    <th class="border px-3 py-2">Capaian</th>
                    <th class="border px-3 py-2">Skor</th>
                    <th class="border px-3 py-2">Grade</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rincian as $r)
                <tr>
                    <td class="border px-3 py-2">{{ $r->nama }}</td>
                    <td class="border px-3 py-2">{{ $r->total_target }}</td>
                    <td class="border px-3 py-2">{{ $r->total_realisasi }}</td>
                    <td class="border px-3 py-2">{{ $r->kuantitas ?? 0 }}%</td>
                    <td class="border px-3 py-2">{{ $r->kualitas ?? 0 }}%</td>
                    <td class="border px-3 py-2">{{ $r->capaian }}%</td>
                    <td class="border px-3 py-2">{{ number_format($r->skor,2) }}</td>
                    <td class="border px-3 py-2">
                        <span class="px-2 py-1 rounded text-xs
                                @if($r->grade == 'Sangat Baik') bg-green-200 text-green-800
                                @elseif($r->grade == 'Baik') bg-blue-200 text-blue-800
                                @elseif($r->grade == 'Cukup') bg-yellow-200 text-yellow-800
                                @else bg-red-200 text-red-800 @endif">
                            {{ $r->grade }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-3 text-gray-500">Belum ada data pegawai</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Grafik Workload -->
    <div class="bg-white shadow rounded-xl p-6">
        <h2 class="text-lg font-bold mb-4">Grafik Workload Pegawai</h2>
        <canvas id="workloadChart"></canvas>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('workloadChart').getContext('2d');
    const data = @json($chartData);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.nama),
            datasets: [{
                    label: 'Target',
                    data: data.map(d => d.target),
                    backgroundColor: 'rgba(59, 130, 246, 0.7)' // biru
                },
                {
                    label: 'Realisasi',
                    data: data.map(d => d.realisasi),
                    backgroundColor: 'rgba(16, 185, 129, 0.7)' // hijau
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<!-- Footer -->
<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
    © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>
@endsection