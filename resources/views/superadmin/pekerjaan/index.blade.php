@extends('layouts.app')

@section('title', 'Data Pekerjaan')

@section('content')
<div class="card mb-4 shadow-sm">
  <div class="card-body">

  <h3 class="mb-3">Data Progress</h3>
    <!-- Progress bar -->
    <div class="mb-3">
      <div class="progress" style="height: 25px;">
        <div 
          class="progress-bar bg-success" 
          role="progressbar" 
          style="width: {{ $persentaseSelesai }}%;" 
          aria-valuenow="{{ $persentaseSelesai }}" 
          aria-valuemin="0" 
          aria-valuemax="100">
          {{ $persentaseSelesai }}%
        </div>
      </div>
    </div>

    <!-- Detail statistik -->
    <div class="row text-center">
      <div class="col-md-2 offset-md-1">
        <strong>Persentase</strong><br>{{ $persentaseSelesai }}%
      </div>
      <div class="col-md-2">
        <strong>Total Tugas</strong><br>{{ $totalTugas }}
      </div>
      <div class="col-md-2">
        <strong>Selesai</strong><br>{{ $tugasSelesai }}
      </div>
      <div class="col-md-2">
        <strong>Ongoing</strong><br>{{ $tugasOngoing }}
      </div>
      <div class="col-md-2">
        <strong>Belum</strong><br>{{ $tugasBelum }}
      </div>
    </div>
  </div>
</div>

<div class="card mb-4 shadow-sm">
  <div class="card-body">
<h3 class="mb-3">Tabel Pekerjaan</h3>
<!-- Form Pencarian dan Filter -->
<form method="GET" action="{{ route('superadmin.pekerjaan.index') }}">
  <div class="row mb-3">
    <div class="col-md-3">
      <input type="text" name="search" class="form-control" placeholder="Cari Nama Tugas" value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
      <select name="deadline_month" class="form-control">
        <option value="">Bulan Deadline</option>
        @for($i = 1; $i <= 12; $i++)
          <option value="{{ $i }}" {{ request('deadline_month') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->format('F') }}</option>
        @endfor
      </select>
    </div>
    <div class="col-md-2">
      <select name="deadline_year" class="form-control">
        <option value="">Tahun Deadline</option>
        @for($i = 2020; $i <= now()->year; $i++)
          <option value="{{ $i }}" {{ request('deadline_year') == $i ? 'selected' : '' }}>{{ $i }}</option>
        @endfor
      </select>
    </div>
    <div class="col-md-2">
      <select name="realisasi_month" class="form-control">
        <option value="">Bulan Realisasi</option>
        @for($i = 1; $i <= 12; $i++)
          <option value="{{ $i }}" {{ request('realisasi_month') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->format('F') }}</option>
        @endfor
      </select>
    </div>
    <div class="col-md-2">
      <select name="realisasi_year" class="form-control">
        <option value="">Tahun Realisasi</option>
        @for($i = 2020; $i <= now()->year; $i++)
          <option value="{{ $i }}" {{ request('realisasi_year') == $i ? 'selected' : '' }}>{{ $i }}</option>
        @endfor
      </select>
    </div>
    <div class="col-md-1">
      <button type="submit" class="btn btn-primary">Filter</button>
    </div>
  </div>
</form>

<!-- Data Tugas Table -->
<table class="table table-bordered table-sm">
  <thead class="table-primary text-center">
    <tr>
      <th>No.</th>
      <th>Nama Tugas</th>
      <th>Bobot</th>
      <th>Asal</th>
      <th>Target</th>
      <th>Realisasi</th>
      <th>Satuan</th>
      <th>Deadline</th>
      <th>Tanggal Realisasi</th>
      <th>Nilai Kualitas</th>
      <th>Nilai Kuantitas</th>
      <th>Catatan</th>
      <th>Bukti</th>
    </tr>
  </thead>
  <tbody>
    @forelse($tugas as $tugas)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $tugas->nama_tugas }}</td>
        <td class="text-center">{{ $tugas->jenisPekerjaan->bobot ?? 0 }}</td>
        <td>{{ $tugas->asal ?? '-' }}</td>
        <td class="text-center">{{ $tugas->target }}</td>
        <td class="text-center">{{ $tugas->realisasi->realisasi ?? '-' }}</td>
        <td>{{ $tugas->satuan }}</td>
        <td>{{ \Carbon\Carbon::parse($tugas->deadline)->format('d M Y') }}</td>
        <td>{{ optional($tugas->realisasi)->tanggal_realisasi ?? '-' }}</td>
        <td class="text-center">{{ $tugas->realisasi->nilai_kualitas ?? '-' }}</td>
        <td class="text-center">{{ $tugas->realisasi->nilai_kuantitas ?? '-' }}</td>
        <td class="text-center">{{ $tugas->realisasi->catatan ?? '-' }}</td>
        <td class="text-center">
          @if($tugas->realisasi && $tugas->realisasi->file_bukti)
            <a href="{{ asset('storage/' . $tugas->realisasi->file_bukti) }}" target="_blank">Lihat</a>
          @else
            -
          @endif
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="13" class="text-center">Tidak ada data pekerjaan yang tersedia.</td>
      </tr>
    @endforelse
  </tbody>
</table>
</div>
</div>
@endsection
