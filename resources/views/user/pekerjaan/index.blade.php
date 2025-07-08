@extends('layouts.app')
@section('title', 'Tugas Saya')

@section('content')
<h4>Daftar Tugas Anda</h4>

@forelse($tugas as $t)
<div class="card mb-4">
  <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
    <strong>{{ $t->nama_tugas }} - {{ $t->jenisPekerjaan->nama_pekerjaan }}</strong>

    @if(!$t->realisasi)
      <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalRealisasi{{ $t->id }}">
        Isi Realisasi
      </button>
    @else
      <div>
        <span class="badge bg-success me-2">Sudah Dikerjakan</span>
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditRealisasi{{ $t->realisasi->id }}">
          Edit Realisasi
        </button>
      </div>
    @endif
  </div>
  <div class="card-body">
    <p><strong>Target:</strong> {{ $t->target }} {{ $t->satuan }}</p>
    <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y') }}</p>

    @if($t->realisasi)
      <div class="alert alert-success">
        <strong>Sudah Dikerjakan</strong><br>
        Realisasi: {{ $t->realisasi->realisasi }}<br>
        Tanggal: {{ $t->realisasi->tanggal_realisasi }}<br>
        Kualitas: {{ $t->realisasi->nilai_kualitas }} | Kuantitas: {{ $t->realisasi->nilai_kuantitas }}<br>
        @if($t->realisasi->file_bukti)
          <a href="{{ asset('storage/'.$t->realisasi->file_bukti) }}" target="_blank">Lihat Bukti</a>
        @endif
      </div>
    @endif
  </div>
</div>

<!-- Modal Tambah Realisasi -->
@if(!$t->realisasi)
<div class="modal fade" id="modalRealisasi{{ $t->id }}" tabindex="-1" aria-labelledby="modalRealisasiLabel{{ $t->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('user.pekerjaan.realisasi', $t->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRealisasiLabel{{ $t->id }}">Realisasi Tugas: {{ $t->nama_tugas }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Realisasi</label>
            <input type="number" name="realisasi" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Tanggal Realisasi</label>
            <input type="date" name="tanggal_realisasi" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Nilai Kualitas</label>
            <input type="number" name="nilai_kualitas" class="form-control" min="0" max="100">
          </div>
          <div class="mb-3">
            <label>Nilai Kuantitas</label>
            <input type="number" name="nilai_kuantitas" class="form-control" min="0" max="100">
          </div>
          <div class="mb-3">
            <label>Catatan</label>
            <textarea name="catatan" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label>Upload Bukti (PDF, Gambar)</label>
            <input type="file" name="file_bukti" class="form-control" accept=".pdf,image/*">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Kirim Realisasi</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endif

<!-- Modal Edit Realisasi -->
@if($t->realisasi)
<div class="modal fade" id="modalEditRealisasi{{ $t->realisasi->id }}" tabindex="-1" aria-labelledby="modalEditRealisasiLabel{{ $t->realisasi->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('user.pekerjaan.realisasi.update', $t->realisasi->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditRealisasiLabel{{ $t->realisasi->id }}">Edit Realisasi Tugas: {{ $t->nama_tugas }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Realisasi</label>
            <input type="number" name="realisasi" class="form-control" value="{{ $t->realisasi->realisasi }}" required>
          </div>
          <div class="mb-3">
            <label>Tanggal Realisasi</label>
            <input type="date" name="tanggal_realisasi" class="form-control" value="{{ $t->realisasi->tanggal_realisasi }}" required>
          </div>
          <div class="mb-3">
            <label>Nilai Kualitas</label>
            <input type="number" name="nilai_kualitas" class="form-control" min="0" max="100" value="{{ $t->realisasi->nilai_kualitas }}">
          </div>
          <div class="mb-3">
            <label>Nilai Kuantitas</label>
            <input type="number" name="nilai_kuantitas" class="form-control" min="0" max="100" value="{{ $t->realisasi->nilai_kuantitas }}">
          </div>
          <div class="mb-3">
            <label>Catatan</label>
            <textarea name="catatan" class="form-control">{{ $t->realisasi->catatan }}</textarea>
          </div>
          <div class="mb-3">
            <label>Upload Bukti (PDF, Gambar)</label>
            <input type="file" name="file_bukti" class="form-control" accept=".pdf,image/*">
            @if($t->realisasi->file_bukti)
              <small class="text-muted">File saat ini: <a href="{{ asset('storage/'.$t->realisasi->file_bukti) }}" target="_blank">Lihat Bukti</a></small>
            @endif
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">Update Realisasi</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endif
@empty
  <div class="alert alert-info">
    Tidak ada tugas yang tersedia saat ini.
  </div>
@endforelse

@endsection
