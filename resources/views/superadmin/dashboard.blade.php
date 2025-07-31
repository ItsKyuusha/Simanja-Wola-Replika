@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div x-data="{ tab: 'kegiatan' }" class="container mx-auto px-4 py-6">

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
            <h5 class="font-bold text-blue-700 mb-3">Jumlah Kegiatan Pegawai <span class="text-sm font-normal text-gray-400">July</span></h5>

            <div class="flex justify-between items-center mb-3">
                <button class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded text-sm">
                    <i class="fas fa-file-export mr-1"></i> Export Tabel
                </button>
                <div class="relative">
                    <input type="text" placeholder="Search..." class="border border-gray-300 px-3 py-1 rounded text-sm">
                </div>
            </div>

            <div class="overflow-auto">
                <table class="table-auto w-full text-sm border border-gray-200">
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
        </div>
    </div>

    <!-- Tab: Jumlah Bobot -->
    <div x-show="tab === 'bobot'" x-transition>
        <div class="bg-white rounded shadow p-4 border">
            <h5 class="font-bold text-blue-700 mb-3">Jumlah Bobot Pegawai</h5>
            <div class="overflow-auto">
                <table class="table-auto w-full text-sm border border-gray-200">
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
        </div>
    </div>

    <!-- Tab: Nilai Kinerja -->
    <div x-show="tab === 'kinerja'" x-transition>
        <div class="bg-white rounded shadow p-4 border">
            <h5 class="font-bold text-blue-700 mb-3">Nilai Kinerja Pegawai</h5>
            <div class="overflow-auto">
                <table class="table-auto w-full text-sm border border-gray-200">
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
        </div>
    </div>

    <!-- Tab: Persentase -->
    <div x-show="tab === 'persen'" x-transition>
        <div class="bg-white rounded shadow p-4 border">
            <h5 class="font-bold text-blue-700 mb-3">Persentase Selesai per Pegawai</h5>
            <div class="overflow-auto">
                <table class="table-auto w-full text-sm border border-gray-200">
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
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
        © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
    </footer>
</div>
@endsection
