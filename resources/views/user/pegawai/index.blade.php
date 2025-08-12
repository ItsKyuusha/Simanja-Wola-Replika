@extends('layouts.app')
@section('page-title', 'Pegawai')

@section('content')
 <div class="bg-white rounded-xl p-6 border border-gray-200">
    
    <!-- Judul + Form Pencarian dalam 1 Baris -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 flex-wrap">
        <h4 class="text-xl font-semibold text-gray-800">Daftar Anggota Tim Anda</h4>
        
        <form method="GET" action="{{ route('user.pegawai.index') }}" class="flex gap-2">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Cari Nama atau NIP..." 
                class="w-full sm:w-72 border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200"
            >
            <button 
                type="submit" 
                class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-md text-gray-700 border border-gray-300 text-sm"
            >
                Cari
            </button>
        </form>
    </div>

    <!-- Tabel Anggota Tim -->
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border border-gray-300 text-sm text-left">
            <thead class="bg-blue-100 text-gray-700">
                <tr>
                    <th class="px-2 py-2 border w-12 text-center">No.</th>
                    <th class="px-4 py-2 border">Nama</th>
                    <th class="px-4 py-2 border">NIP</th>
                    <th class="px-4 py-2 border">Jabatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pegawai as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-2 py-2 border text-center w-12">{{ $loop->iteration }}</td>
                    <td class="px-4 py-2 border">{{ $p->nama }}</td>
                    <td class="px-4 py-2 border">{{ $p->nip }}</td>
                    <td class="px-4 py-2 border">{{ $p->jabatan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-4 border text-center text-gray-500">
                        Tidak ada data pegawai yang tersedia.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<footer class="text-center text-sm text-gray-500 py-4 border-t mt-8">
  © {{ date('Y') }} <strong>WOLA</strong>. All rights reserved.
</footer>
@endsection
