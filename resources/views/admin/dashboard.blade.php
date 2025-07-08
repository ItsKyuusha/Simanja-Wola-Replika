@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<h4>Dashboard Tim: {{ auth()->user()->pegawai->team->nama_tim ?? '-' }}</h4>

<div class="row mt-4">
  <div class="col-md-4">
    <div class="small-box bg-primary">
      <div class="inner">
        <h3>{{ $totalTugas }}</h3>
        <p>Total Tugas Tim</p>
      </div>
      <div class="icon"><i class="fas fa-tasks"></i></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>{{ $jumlahPegawai }}</h3>
        <p>Jumlah Anggota Tim</p>
      </div>
      <div class="icon"><i class="fas fa-users"></i></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>{{ $mostActive->nama ?? '-' }}</h3>
        <p>Anggota Teraktif</p>
      </div>
      <div class="icon"><i class="fas fa-star"></i></div>
    </div>
  </div>
</div>
@endsection
