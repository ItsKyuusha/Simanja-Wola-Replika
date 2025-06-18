<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Superadmin Dashboard</title>
  <!-- Bootstrap 5 CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <div class="container mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
      <div class="card-body text-center">
        <h2 class="card-title mb-4">Superadmin Dashboard</h2>
        <p class="lead">Selamat datang, {{ auth()->user()->name }}!</p>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn-danger mt-3">Logout</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle (optional) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
