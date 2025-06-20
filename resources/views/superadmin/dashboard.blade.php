@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-success text-white text-center">
          <h4 class="mb-0">Superadmin Dashboard</h4>
        </div>
        <div class="card-body text-center">
          <p class="lead">Selamat datang, <strong>{{ auth()->user()->name }}</strong>!</p>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger mt-3">
              <i class="fas fa-sign-out-alt me-2"></i>Logout
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
