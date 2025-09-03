@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div x-data="{ tab: 'kegiatan' }" class="space-y-6">

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white shadow rounded-lg p-4 border-l-4 border-blue-600">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-blue-600 font-medium">PROJECT</p>
                    <h2 class="text-2xl font-bold">0</h2>
                    <p class="text-sm text-gray-500">0 Completed</p>
                </div>
                <i class="fas fa-project-diagram text-2xl text-blue-600"></i>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-green-600 font-medium">TEAM</p>
                    <h2 class="text-2xl font-bold">{{ $totalTeam }}</h2>
                </div>
                <i class="fas fa-users text-2xl text-green-500"></i>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 border-l-4 border-cyan-500">
            <div class="flex justify-between items-center w-full">
                <div class="w-full">
                    <p class="text-sm text-cyan-600 font-medium">PRODUKTIVITAS</p>
                    <h2 class="text-2xl font-bold">0%</h2>
                    <div class="w-full bg-gray-200 h-2 rounded-full mt-1">
                        <div class="bg-cyan-500 h-2 w-[0%] rounded-full"></div>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">0 Completed</p>
                </div>
                <i class="fas fa-chart-line text-2xl text-cyan-500"></i>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 border-l-4 border-yellow-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-yellow-600 font-medium">MOST ACTIVE</p>
                    <h2 class="text-2xl font-bold">0</h2>
                    <p class="text-sm text-gray-500">Completed</p>
                </div>
                <i class="fas fa-trophy text-2xl text-yellow-500"></i>
            </div>
        </div>
    </div>

    <!-- FILTER Bulan & Tahun -->
    <form method="GET" action="{{ route('superadmin.dashboard') }}" class="flex flex-wrap items-center gap-2 mb-4">
        <!-- Bulan -->
        <select name="bulan" class="px-3 py-2 rounded bg-gray-100 text-sm">
            <option value="" {{ request('bulan') == '' ? 'selected' : '' }}>Semua Bulan</option>
            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $index => $bulan)
            <option value="{{ $index + 1 }}" {{ request('bulan') == ($index + 1) ? 'selected' : '' }}>{{ $bulan }}</option>
            @endforeach
        </select>

        <!-- Tahun -->
        <select name="tahun" class="px-3 py-2 rounded bg-gray-100 text-sm">
            <option value="" {{ request('tahun') == '' ? 'selected' : '' }}>Semua Tahun</option>
            @for($tahun = 2020; $tahun <= 2025; $tahun++)
                <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                @endfor
        </select>

        <!-- Tombol Filter -->
        <button type="submit" class="px-4 py-2 rounded text-white bg-blue-600 hover:bg-blue-700 text-sm">
            Filter
        </button>
    </form>

    <!-- Tab Navigation -->
    <div class="mb-4 flex flex-wrap gap-2">
        <button @click="tab = 'kegiatan'" :class="tab === 'kegiatan' ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 border border-blue-600'"
            class="px-4 py-1 rounded-full text-sm font-medium shadow-sm transition">
            Jumlah Kegiatan Pegawai
        </button>
        <button @click="tab = 'bobot'" :class="tab === 'bobot' ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 border border-blue-600'"
            class="px-4 py-1 rounded-full text-sm font-medium shadow-sm transition">
            Jumlah Bobot Pekerjaan Pegawai
        </button>
        <button @click="tab = 'kinerja'" :class="tab === 'kinerja' ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 border border-blue-600'"
            class="px-4 py-1 rounded-full text-sm font-medium shadow-sm transition">
            Nilai Kinerja Pegawai
        </button>
        <button @click="tab = 'persen'" :class="tab === 'persen' ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 border border-blue-600'"
            class="px-4 py-1 rounded-full text-sm font-medium shadow-sm transition">
            Persentase Tugas Selesai Tim
        </button>
        <button @click="tab = ''"
            class="bg-red-500 text-white px-4 py-1 rounded-full text-sm font-medium shadow-sm hover:bg-red-600 transition">
            Tutup Semua
        </button>
    </div>



    <!-- Tab: Jumlah Kegiatan -->
    <div x-show="tab === 'kegiatan'" x-transition>
        <div class="bg-white rounded shadow p-4 border">
            <h5 class="font-bold text-blue-700 mb-3 text-lg">
                Jumlah Kegiatan Pegawai
                @if($labelBulanTahun)
                <span class="text-lg font-bold text-blue-600 ml-2">({{ $labelBulanTahun }})</span>
                @endif
            </h5>

            <div class="flex justify-between items-center flex-wrap gap-2 mb-3">
                <div class="flex gap-2">
                    <!-- Tombol Export Dinamis -->
                    <button id="exportBtn" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded text-sm">
                        <i class="fas fa-file-export mr-1"></i> Export Tabel
                    </button>

                    <button id="toggleChartBtn" class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded text-sm">
                        <i class="fas fa-chart-bar mr-1"></i> Tampilkan Grafik
                    </button>
                </div>

                <form method="GET" action="{{ route('superadmin.dashboard') }}" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai..."
                        class="border border-gray-300 px-3 py-1 rounded text-sm placeholder-black" />
                    <!-- Filter tetap terbawa saat search -->
                    <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                    <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                </form>
            </div>

            <!-- TABEL -->
            <div class="overflow-auto">
                <table id="tabelKegiatan" class="table-auto w-full text-sm border border-gray-200">
                    <thead class="bg-blue-100 text-center text-sm text-gray-700">
                        <tr>
                            <th class="px-4 py-2 border">Nama Pegawai</th>
                            <th class="px-4 py-2 border">Jumlah Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jumlahKegiatan as $item)
                        <tr class="text-center">
                            <td class="px-4 py-2 border text-left">{{ $item->nama }}</td>
                            <td class="px-4 py-2 border">{{ $item->tugas_count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-gray-500 py-3">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- GRAFIK -->
            <div id="chartContainer" class="hidden mt-4 overflow-x-auto">
                <div id="chartWrapper" class="min-w-full">
                    <canvas id="kegiatanChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Jumlah Bobot -->
    <div x-show="tab === 'bobot'" x-transition>
        <div class="bg-white rounded shadow p-4 border">
            <h5 class="font-bold text-blue-700 mb-3 text-lg">
                Jumlah Bobot Pegawai
                @if($labelBulanTahun)
                <span class="text-lg font-bold text-blue-600 ml-2">({{ $labelBulanTahun }})</span>
                @endif
            </h5>

            <div class="flex justify-between items-center flex-wrap gap-2 mb-3">
                <div class="flex gap-2">
                    <!-- Tombol Export -->
                    <button id="exportBtnBobot" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded text-sm">
                        <i class="fas fa-file-export mr-1"></i> Export Tabel
                    </button>

                    <!-- Toggle Grafik -->
                    <button id="toggleChartBtnBobot" class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded text-sm">
                        <i class="fas fa-chart-bar mr-1"></i> Tampilkan Grafik
                    </button>
                </div>

                <!-- Search -->
                <form method="GET" action="{{ route('superadmin.dashboard') }}" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai..."
                        class="border border-gray-300 px-3 py-1 rounded text-sm placeholder-black" />
                    <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                    <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                </form>
            </div>

            <!-- TABEL -->
            <div id="tableContainerBobot" class="overflow-auto">
                <table id="tabelBobot" class="table-auto w-full text-sm border border-gray-200">
                    <thead class="bg-blue-100 text-center text-sm text-gray-700">
                        <tr>
                            <th class="px-4 py-2 border">Nama Pegawai</th>
                            <th class="px-4 py-2 border">Total Bobot</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bobotPerPegawai as $item)
                        <tr class="text-center">
                            <td class="px-4 py-2 border text-left">{{ $item->nama }}</td>
                            <td class="px-4 py-2 border">{{ $item->total_bobot }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-gray-500 py-3">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- GRAFIK -->
            <div id="chartContainerBobot" class="hidden mt-4 overflow-x-auto">
                <div class="min-w-full">
                    <canvas id="bobotChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Nilai Kinerja -->
    <div x-show="tab === 'kinerja'" x-transition>
        <div class="bg-white rounded shadow p-4 border">
            <h5 class="font-bold text-blue-700 mb-3 text-lg">
                Nilai Kinerja Pegawai
                @if($labelBulanTahun)
                <span class="text-lg font-bold text-blue-600 ml-2">({{ $labelBulanTahun }})</span>
                @endif
            </h5>

            <div class="flex justify-between items-center flex-wrap gap-2 mb-3">
                <div class="flex gap-2">
                    <button id="exportBtnKinerja" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded text-sm">
                        <i class="fas fa-file-export mr-1"></i> Export Tabel
                    </button>

                    <button id="toggleChartBtnKinerja" class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded text-sm">
                        <i class="fas fa-chart-bar mr-1"></i> Tampilkan Grafik
                    </button>
                </div>

                <!-- Search -->
                <form method="GET" action="{{ route('superadmin.dashboard') }}" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai..."
                        class="border border-gray-300 px-3 py-1 rounded text-sm placeholder-black" />
                    <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                    <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                </form>
            </div>

            <!-- TABEL -->
            <div id="tableContainerKinerja" class="overflow-auto">
                <table id="tabelKinerja" class="table-auto w-full text-sm border border-gray-200">
                    <thead class="bg-blue-100 text-center text-sm text-gray-700">
                        <tr>
                            <th class="px-4 py-2 border">Nama Pegawai</th>
                            <th class="px-4 py-2 border">Nilai Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nilaiKinerja as $item)
                        <tr class="text-center">
                            <td class="px-4 py-2 border text-left">{{ $item->pegawai->nama }}</td>
                            <td class="px-4 py-2 border">{{ $item->nilai_akhir }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-gray-500 py-3">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- GRAFIK -->
            <div id="chartContainerKinerja" class="hidden mt-4 overflow-x-auto">
                <div class="min-w-full">
                    <canvas id="kinerjaChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Persentase -->
    <div x-show="tab === 'persen'" x-transition>
        <div class="bg-white rounded shadow p-4 border">
            <h5 class="font-bold text-blue-700 mb-3 text-lg">
                Persentase Selesai per Pegawai
                @if($labelBulanTahun)
                <span class="text-lg font-bold text-blue-600 ml-2">({{ $labelBulanTahun }})</span>
                @endif
            </h5>

            <!-- Tombol -->
            <div class="flex justify-between items-center flex-wrap gap-2 mb-3">
                <div class="flex gap-2">
                    <button id="exportBtnPersen" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded text-sm">
                        <i class="fas fa-file-export mr-1"></i> Export Tabel
                    </button>

                    <button id="toggleChartBtnPersen" class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded text-sm">
                        <i class="fas fa-chart-bar mr-1"></i> Tampilkan Grafik
                    </button>
                </div>

                <!-- Search -->
                <form method="GET" action="{{ route('superadmin.dashboard') }}" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pegawai..."
                        class="border border-gray-300 px-3 py-1 rounded text-sm placeholder-black" />
                    <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                    <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                </form>
            </div>

            <!-- Tabel -->
            <div class="overflow-auto persenTable">
                <table id="tabelPersen" class="table-auto w-full text-sm border border-gray-200">
                    <thead class="bg-blue-100 text-center text-sm text-gray-700">
                        <tr>
                            <th class="px-4 py-2 border">Nama Pegawai</th>
                            <th class="px-4 py-2 border">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($persentaseSelesai as $item)
                        <tr class="text-center">
                            <td class="px-4 py-2 border text-left">{{ $item->nama }}</td>
                            <td class="px-4 py-2 border">{{ $item->persen_selesai ?? 0 }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-gray-500 py-3">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Grafik -->
            <div id="chartContainerPersen" class="hidden mt-4 overflow-x-auto">
                <div id="chartWrapperPersen" class="min-w-full">
                    <canvas id="persenChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>



    <!-- Footer -->
    <footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
        © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
    </footer>
</div>
@endsection

<!-- Script Grafik -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleChartBtn = document.getElementById('toggleChartBtn');
        const chartContainer = document.getElementById('chartContainer');
        const tableContainer = document.querySelector('.overflow-auto');
        const exportBtn = document.getElementById('exportBtn');
        let chartVisible = false;

        toggleChartBtn.addEventListener('click', () => {
            chartVisible = !chartVisible;

            if (chartVisible) {
                chartContainer.classList.remove('hidden');
                tableContainer.classList.add('hidden');
                toggleChartBtn.innerHTML = '<i class="fas fa-table mr-1"></i> Tampilkan Tabel';
                exportBtn.innerHTML = '<i class="fas fa-file-export mr-1"></i> Export Grafik';
            } else {
                chartContainer.classList.add('hidden');
                tableContainer.classList.remove('hidden');
                toggleChartBtn.innerHTML = '<i class="fas fa-chart-bar mr-1"></i> Tampilkan Grafik';
                exportBtn.innerHTML = '<i class="fas fa-file-export mr-1"></i> Export Tabel';
            }
        });

        // Ambil data dari Blade
        const chartData = @json($jumlahKegiatan);
        const labelBulanTahun = @json($labelBulanTahun ?? "Semua Bulan & Tahun");

        const labels = chartData.map(item => item.nama);
        const dataPoints = chartData.map(item => item.tugas_count);

        const canvas = document.getElementById('kegiatanChart');
        if (chartData.length > 30) {
            canvas.width = chartData.length * 40;
        }

        const ctx = canvas.getContext('2d');
        const kegiatanChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Kegiatan',
                    data: dataPoints,
                    backgroundColor: 'rgba(37, 99, 235, 0.6)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: `Grafik Jumlah Kegiatan Pegawai (${labelBulanTahun})`
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Kegiatan'
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 60,
                            minRotation: 45,
                        },
                        title: {
                            display: true,
                            text: 'Pegawai'
                        }
                    }
                }
            }
        });


        // Fungsi Export
        exportBtn.addEventListener('click', () => {
            if (chartVisible) {
                // Export grafik sebagai gambar PNG
                const link = document.createElement('a');
                link.download = 'grafik-kegiatan.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            } else {
                // Export tabel sebagai CSV
                const table = document.getElementById('tabelKegiatan');
                let csvContent = "";
                const rows = table.querySelectorAll('tr');

                rows.forEach(row => {
                    let rowData = [];
                    row.querySelectorAll('th, td').forEach(cell => {
                        let cellText = cell.innerText.replace(/"/g, '""'); // Escape quote
                        rowData.push(`"${cellText}"`);
                    });
                    csvContent += rowData.join(",") + "\r\n";
                });

                const blob = new Blob([csvContent], {
                    type: 'text/csv;charset=utf-8;'
                });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.setAttribute('download', 'jumlah-kegiatan-pegawai.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleChartBtn = document.getElementById('toggleChartBtnBobot');
        const chartContainer = document.getElementById('chartContainerBobot');
        const tableContainer = document.getElementById('tableContainerBobot');
        const exportBtn = document.getElementById('exportBtnBobot');
        let chartVisible = false;

        toggleChartBtn.addEventListener('click', () => {
            chartVisible = !chartVisible;

            if (chartVisible) {
                chartContainer.classList.remove('hidden');
                tableContainer.classList.add('hidden');
                toggleChartBtn.innerHTML = '<i class="fas fa-table mr-1"></i> Tampilkan Tabel';
                exportBtn.innerHTML = '<i class="fas fa-file-export mr-1"></i> Export Grafik';
            } else {
                chartContainer.classList.add('hidden');
                tableContainer.classList.remove('hidden');
                toggleChartBtn.innerHTML = '<i class="fas fa-chart-bar mr-1"></i> Tampilkan Grafik';
                exportBtn.innerHTML = '<i class="fas fa-file-export mr-1"></i> Export Tabel';
            }
        });

        const chartData = @json($bobotPerPegawai);
        const labelBulanTahun = @json($labelBulanTahun ?? "Semua Bulan & Tahun");

        const labels = chartData.map(item => item.nama);
        const dataPoints = chartData.map(item => item.total_bobot);

        const canvas = document.getElementById('bobotChart');
        if (chartData.length > 30) {
            canvas.width = chartData.length * 40;
        }

        const ctx = canvas.getContext('2d');
        const bobotChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Bobot',
                    data: dataPoints,
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: 'rgba(5, 150, 105, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: `Grafik Jumlah Bobot Pegawai (${labelBulanTahun})`
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Bobot'
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 60,
                            minRotation: 45,
                        },
                        title: {
                            display: true,
                            text: 'Pegawai'
                        }
                    }
                }
            }
        });

        exportBtn.addEventListener('click', () => {
            if (chartVisible) {
                const link = document.createElement('a');
                link.download = 'grafik-bobot.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            } else {
                const table = document.getElementById('tabelBobot');
                let csvContent = "";
                const rows = table.querySelectorAll('tr');

                rows.forEach(row => {
                    let rowData = [];
                    row.querySelectorAll('th, td').forEach(cell => {
                        let cellText = cell.innerText.replace(/"/g, '""');
                        rowData.push(`"${cellText}"`);
                    });
                    csvContent += rowData.join(",") + "\r\n";
                });

                const blob = new Blob([csvContent], {
                    type: 'text/csv;charset=utf-8;'
                });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.setAttribute('download', 'jumlah-bobot-pegawai.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleChartBtnKinerja = document.getElementById('toggleChartBtnKinerja');
        const chartContainerKinerja = document.getElementById('chartContainerKinerja');
        const tableContainerKinerja = document.getElementById('tableContainerKinerja');
        const exportBtnKinerja = document.getElementById('exportBtnKinerja');
        let chartVisibleKinerja = false;

        toggleChartBtnKinerja.addEventListener('click', () => {
            chartVisibleKinerja = !chartVisibleKinerja;

            if (chartVisibleKinerja) {
                chartContainerKinerja.classList.remove('hidden');
                tableContainerKinerja.classList.add('hidden');
                toggleChartBtnKinerja.innerHTML = '<i class="fas fa-table mr-1"></i> Tampilkan Tabel';
                exportBtnKinerja.innerHTML = '<i class="fas fa-file-export mr-1"></i> Export Grafik';
            } else {
                chartContainerKinerja.classList.add('hidden');
                tableContainerKinerja.classList.remove('hidden');
                toggleChartBtnKinerja.innerHTML = '<i class="fas fa-chart-bar mr-1"></i> Tampilkan Grafik';
                exportBtnKinerja.innerHTML = '<i class="fas fa-file-export mr-1"></i> Export Tabel';
            }
        });

        const kinerjaData = @json($nilaiKinerja);
        const labelBulanTahun = @json($labelBulanTahun ?? "Semua Bulan & Tahun");

        const kinerjaLabels = kinerjaData.map(item => item.pegawai.nama);
        const nilaiData = kinerjaData.map(item => item.nilai_akhir);

        const canvasKinerja = document.getElementById('kinerjaChart');
        if (kinerjaData.length > 30) {
            canvasKinerja.width = kinerjaData.length * 40;
        }

        const ctxKinerja = canvasKinerja.getContext('2d');
        const kinerjaChart = new Chart(ctxKinerja, {
            type: 'bar',
            data: {
                labels: kinerjaLabels,
                datasets: [{
                    label: 'Nilai Akhir',
                    data: nilaiData,
                    backgroundColor: 'rgba(0, 188, 212, 0.6)',
                    borderColor: 'rgba(0, 188, 212, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: `Grafik Nilai Kinerja Pegawai (${labelBulanTahun})`
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nilai Akhir'
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 60,
                            minRotation: 45,
                        },
                        title: {
                            display: true,
                            text: 'Pegawai'
                        }
                    }
                }
            }
        });

        exportBtnKinerja.addEventListener('click', () => {
            if (chartVisibleKinerja) {
                const link = document.createElement('a');
                link.download = 'grafik-nilai-kinerja.png';
                link.href = canvasKinerja.toDataURL('image/png');
                link.click();
            } else {
                const table = document.getElementById('tabelKinerja');
                let csvContent = "";
                const rows = table.querySelectorAll('tr');

                rows.forEach(row => {
                    let rowData = [];
                    row.querySelectorAll('th, td').forEach(cell => {
                        let cellText = cell.innerText.replace(/"/g, '""');
                        rowData.push(`"${cellText}"`);
                    });
                    csvContent += rowData.join(",") + "\r\n";
                });

                const blob = new Blob([csvContent], {
                    type: 'text/csv;charset=utf-8;'
                });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.setAttribute('download', 'nilai-kinerja-pegawai.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleChartBtnPersen = document.getElementById('toggleChartBtnPersen');
        const exportBtnPersen = document.getElementById('exportBtnPersen');
        const chartContainerPersen = document.getElementById('chartContainerPersen');
        const tableContainerPersen = document.querySelector('.persenTable');
        let chartVisiblePersen = false;

        // Data dari Blade
        const chartDataPersen = @json($persentaseSelesai);
        const labelBulanTahun = @json($labelBulanTahun ?? "Semua Bulan & Tahun");

        const labelsPersen = chartDataPersen.map(item => item.nama);
        const dataPointsPersen = chartDataPersen.map(item => item.persen_selesai);

        const canvasPersen = document.getElementById('persenChart');
        if (chartDataPersen.length > 30) {
            canvasPersen.width = chartDataPersen.length * 40;
        }

        const ctxPersen = canvasPersen.getContext('2d');
        const persenChart = new Chart(ctxPersen, {
            type: 'bar',
            data: {
                labels: labelsPersen,
                datasets: [{
                    label: 'Persentase Tugas Selesai (%)',
                    data: dataPointsPersen,
                    backgroundColor: 'rgba(241, 177, 0, 0.6)',
                    borderColor: 'rgba(241, 177, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: `Grafik Persentase Tugas Selesai (${labelBulanTahun})`
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Persentase (%)'
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 60,
                            minRotation: 45,
                        },
                        title: {
                            display: true,
                            text: 'Pegawai'
                        }
                    }
                }
            }
        });

        // Toggle Grafik/Tabel
        toggleChartBtnPersen.addEventListener('click', () => {
            chartVisiblePersen = !chartVisiblePersen;

            if (chartVisiblePersen) {
                chartContainerPersen.classList.remove('hidden');
                tableContainerPersen.classList.add('hidden');
                toggleChartBtnPersen.innerHTML = '<i class="fas fa-table mr-1"></i> Tampilkan Tabel';
                exportBtnPersen.innerHTML = '<i class="fas fa-file-export mr-1"></i> Export Grafik';
            } else {
                chartContainerPersen.classList.add('hidden');
                tableContainerPersen.classList.remove('hidden');
                toggleChartBtnPersen.innerHTML = '<i class="fas fa-chart-bar mr-1"></i> Tampilkan Grafik';
                exportBtnPersen.innerHTML = '<i class="fas fa-file-export mr-1"></i> Export Tabel';
            }
        });

        // Export
        exportBtnPersen.addEventListener('click', () => {
            if (chartVisiblePersen) {
                const link = document.createElement('a');
                link.download = 'grafik-persentase.png';
                link.href = canvasPersen.toDataURL('image/png');
                link.click();
            } else {
                const table = document.getElementById('tabelPersen');
                let csvContent = "";
                const rows = table.querySelectorAll('tr');
                rows.forEach(row => {
                    let rowData = [];
                    row.querySelectorAll('th, td').forEach(cell => {
                        let cellText = cell.innerText.replace(/"/g, '""');
                        rowData.push(`"${cellText}"`);
                    });
                    csvContent += rowData.join(",") + "\r\n";
                });

                const blob = new Blob([csvContent], {
                    type: 'text/csv;charset=utf-8;'
                });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.setAttribute('download', 'persentase-selesai.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });
    });
</script>