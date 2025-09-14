@extends('layouts.app')
@section('page-title', 'Dashboard User')

@section('content')
<div x-data="{ showTable: true, showChart: false }" class="bg-white rounded-2xl p-6 mb-12 border border-gray-200">

    {{-- Judul & Filter --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 flex-wrap">
        <h2 class="text-2xl font-semibold text-blue-600">Dashboard User</h2>
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <form method="GET" action="{{ route('user.dashboard') }}" class="flex gap-3 w-full sm:w-auto">
                {{-- Dropdown Bulan --}}
                <select name="bulan" class="px-4 py-2 border rounded-lg">
                    <option value="">Semua Bulan</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni',
                              'Juli','Agustus','September','Oktober','November','Desember'] as $index => $bulan)
                        <option value="{{ $index+1 }}" {{ request('bulan') == $index+1 ? 'selected' : '' }}>
                            {{ $bulan }}
                        </option>
                    @endforeach
                </select>

                {{-- Input Tahun --}}
                <input type="number" name="tahun" class="px-4 py-2 border rounded-lg w-24"
                       placeholder="Tahun" value="{{ request('tahun') }}">

                {{-- Search --}}
                <input type="text" name="search" class="px-4 py-2 border rounded-lg"
                       placeholder="Cari nama pekerjaan..." value="{{ request('search') }}">

                {{-- Tombol Filter --}}
                <button type="submit"
                    class="px-4 py-2 rounded-lg border border-blue-400 text-blue-600 font-medium
                           hover:bg-blue-600 hover:text-white transition">
                    Filter
                </button>
            </form>
        </div>
    </div>

    {{-- Statistik Global --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-blue-100 rounded-lg text-center">
            <div class="text-xl font-bold">{{ $totalTugas }}</div>
            <div class="text-sm text-gray-600">Total Tugas</div>
        </div>
        <div class="p-4 bg-green-100 rounded-lg text-center">
            <div class="text-xl font-bold">{{ $totalBobot ?? 0 }}</div>
            <div class="text-sm text-gray-600">Total Bobot</div>
        </div>
        <div class="p-4 bg-yellow-100 rounded-lg text-center">
            <div class="text-xl font-bold">{{ number_format($rincian->avg('nilaiAkhir') ?? 0, 2) }}</div>
            <div class="text-sm text-gray-600">Rata-rata Nilai Akhir</div>
        </div>
    </div>

    {{-- Tombol Aksi Tunggal --}}
    <div class="flex gap-2 mb-4">
        <button @click="showTable = !showTable; showChart = !showChart" 
            class="px-3 py-2 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 text-sm">
            <i :class="showTable ? 'fas fa-chart-bar' : 'fas fa-table' " class="mr-1"></i>
            <span x-text="showTable ? 'Tampilkan Grafik' : 'Tampilkan Tabel'"></span>
        </button>
    </div>

    {{-- Judul Rincian --}}
    <h3 class="text-lg font-semibold text-gray-700 mb-4">
        Rincian Tugas Anda ({{ $labelBulanTahun }})
    </h3>

    {{-- Tabel Tugas --}}
    <div x-show="showTable" x-transition class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full table-auto text-sm text-gray-700">
            <thead class="bg-gradient-to-r from-blue-100 to-blue-200 text-center text-sm text-gray-700">
                <tr>
                    <th class="px-3 py-2 border">Nama Pekerjaan</th>
                    <th class="px-3 py-2 border">Tim</th>
                    <th class="px-3 py-2 border">Target</th>
                    <th class="px-3 py-2 border">Realisasi</th>
                    <th class="px-3 py-2 border">Bobot</th>
                    <th class="px-3 py-2 border">Hari Telat</th>
                    <th class="px-3 py-2 border">Nilai Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rincian as $t)
                <tr class="text-center hover:bg-gray-50">
                    <td class="px-3 py-2 border">{{ $t->nama_pekerjaan }}</td>
                    <td class="px-3 py-2 border">{{ $t->nama_tim ?? '-' }}</td>
                    <td class="px-3 py-2 border">{{ $t->target }}</td>
                    <td class="px-3 py-2 border">{{ $t->realisasi ?? 0 }}</td>
                    <td class="px-3 py-2 border">{{ $t->bobot }}</td>
                    <td class="px-3 py-2 border">{{ $t->hariTelat }}</td>
                    <td class="px-3 py-2 border">{{ number_format($t->nilaiAkhir, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center px-3 py-4 border text-gray-500">Belum ada tugas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Grafik Target vs Realisasi --}}
    <div x-show="showChart" x-transition class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Grafik Target vs Realisasi Anda</h3>
        <canvas id="grafikUser" height="120"></canvas>
    </div>

</div>

{{-- Footer --}}
<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
    © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('grafikUser').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($rincian->pluck('nama_pekerjaan')),
            datasets: [
                {
                    label: 'Target',
                    data: @json($rincian->pluck('target')),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                },
                {
                    label: 'Realisasi',
                    data: @json($rincian->pluck('realisasi')),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endsection
