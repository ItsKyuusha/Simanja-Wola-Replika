<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register</title>
  <!-- Bootstrap 5 CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <h2 class="text-center mb-4">Register</h2>

        @if($errors->any())
          <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
          @csrf

          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input
              type="text"
              class="form-control"
              id="name"
              name="name"
              placeholder="Name"
              required
              autofocus
            />
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input
              type="email"
              class="form-control"
              id="email"
              name="email"
              placeholder="Email"
              required
            />
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
              type="password"
              class="form-control"
              id="password"
              name="password"
              placeholder="Password"
              required
            />
          </div>

          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input
              type="password"
              class="form-control"
              id="password_confirmation"
              name="password_confirmation"
              placeholder="Confirm Password"
              required
            />
          </div>

          <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <div class="mt-3 text-center">
          <a href="{{ route('login') }}">Sudah punya akun? Login</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle (optional) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
