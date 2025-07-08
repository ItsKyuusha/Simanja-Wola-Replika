@extends('layouts.app')
@section('title', 'Dashboard User')

@section('content')
<h4>Dashboard Anda</h4>

<div class="row mt-4">
  <div class="col-md-6">
    <div class="small-box bg-primary">
      <div class="inner">
        <h3>{{ $totalTugas }}</h3>
        <p>Total Tugas</p>
      </div>
      <div class="icon"><i class="fas fa-tasks"></i></div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>{{ $totalRealisasi }}</h3>
        <p>Total Realisasi</p>
      </div>
      <div class="icon"><i class="fas fa-check-circle"></i></div>
    </div>
  </div>
</div>
@endsection
