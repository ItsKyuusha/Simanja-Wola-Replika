@extends('layouts.app')
@section('page-title', 'Dashboard Tim')

@section('content')
<div class="bg-white rounded-2xl p-6 mb-12 border border-gray-200">

    {{-- Judul & Filter --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 flex-wrap">
        <h2 class="text-2xl font-semibold text-blue-600">Dashboard Tim</h2>
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex gap-3 w-full sm:w-auto">
                <input type="month" name="bulan" class="px-4 py-2 border rounded-lg" value="{{ request('bulan') }}">
                <input type="number" name="tahun" class="px-4 py-2 border rounded-lg w-24" placeholder="Tahun" value="{{ request('tahun') }}">
                <button type="submit" class="px-4 py-2 rounded-lg border border-blue-400 text-blue-600 font-medium hover:bg-blue-600 hover:text-white transition">
                    Filter
                </button>
            </form>
        </div>
    </div>

    {{-- Statistik Global --}}
    <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 mb-6">
        <div class="p-4 bg-blue-100 rounded-lg text-center">
            <div class="text-xl font-bold">{{ $totalTugas }}</div>
            <div class="text-sm text-gray-600">Total Tugas</div>
        </div>
        <div class="p-4 bg-green-100 rounded-lg text-center">
            <div class="text-xl font-bold">{{ $tugasSelesai }}</div>
            <div class="text-sm text-gray-600">Selesai</div>
        </div>
        <div class="p-4 bg-yellow-100 rounded-lg text-center">
            <div class="text-xl font-bold">{{ $tugasOngoing }}</div>
            <div class="text-sm text-gray-600">Ongoing</div>
        </div>
        <div class="p-4 bg-red-100 rounded-lg text-center">
            <div class="text-xl font-bold">{{ $tugasBelum }}</div>
            <div class="text-sm text-gray-600">Belum Dikerjakan</div>
        </div>
        <div class="p-4 bg-purple-100 rounded-lg text-center">
            <div class="text-xl font-bold">{{ $rataNilaiAkhir }}</div>
            <div class="text-sm text-gray-600">Rata-rata Nilai Akhir</div>
        </div>
    </div>

    {{-- Grafik Realisasi vs Target --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Realisasi vs Target ({{ $labelBulanTahun }})</h3>
        <canvas id="grafikRealisasiTarget" height="120"></canvas>
    </div>

    {{-- Tabel Tugas --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full table-auto text-sm text-gray-700">
            <thead class="bg-gradient-to-r from-blue-100 to-blue-200 text-center text-sm text-gray-700">
                <tr>
                    <th class="px-3 py-2 border">No.</th>
                    <th class="px-3 py-2 border">Nama Pegawai</th>
                    <th class="px-3 py-2 border">Nama Tim</th>
                    <th class="px-3 py-2 border">Tugas</th>
                    <th class="px-3 py-2 border">Target</th>
                    <th class="px-3 py-2 border">Realisasi</th>
                    <th class="px-3 py-2 border">Histori Perubahan</th>
                    <th class="px-3 py-2 border">Bobot</th>
                    <th class="px-3 py-2 border">Hari Telat</th>
                    <th class="px-3 py-2 border">Nilai Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr class="text-center hover:bg-gray-50">
                    <td class="px-3 py-2 border">{{ $loop->iteration }}</td>
                    <td class="px-3 py-2 border">{{ $task->pegawai->nama ?? '-' }}</td>
                    <td class="px-3 py-2 border">{{ $task->namaTim ?? '-' }}</td>
                    <td class="px-3 py-2 border">{{ $task->jenisPekerjaan->nama_pekerjaan ?? '-' }}</td>
                    <td class="px-3 py-2 border">{{ $task->target }}</td>
                    <td class="px-3 py-2 border">
                        {{ $task->semuaRealisasi->sum('realisasi') ?? 0 }}
                    </td>
                    <td class="px-3 py-2 border text-left">
                        @foreach($task->semuaRealisasi as $r)
                            <div class="mb-1">
                                <span class="text-gray-600 text-xs">
                                    {{ \Carbon\Carbon::parse($r->tanggal_realisasi)->format('d M Y') }}:
                                </span>
                                <span class="text-gray-800">{{ $r->realisasi }}</span>
                                @if(!$r->is_approved)
                                    <span class="text-yellow-500 text-xs">(Menunggu Approve)</span>
                                @endif
                            </div>
                        @endforeach
                    </td>
                    <td class="px-3 py-2 border">{{ $task->bobot }}</td>
                    <td class="px-3 py-2 border">{{ $task->hariTelat }}</td>
                    <td class="px-3 py-2 border">{{ $task->nilaiAkhir }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center px-3 py-4 border text-gray-500">Tidak ada tugas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- Footer --}}
<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
    © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>

{{-- Script Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('grafikRealisasiTarget').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($grafikLabels),
            datasets: [
                {
                    label: 'Target',
                    data: @json($grafikTarget),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                },
                {
                    label: 'Realisasi',
                    data: @json($grafikRealisasi),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
