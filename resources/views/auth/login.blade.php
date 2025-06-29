<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  <!-- Bootstrap 5 CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Custom CSS for styling -->
  <style>
    body {
      background-color: #1565c0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
      flex-direction: column; /* Menambahkan vertikal alignment */
    }

    .logo {
      display: block;
      margin-bottom: 20px;
      width: 250px; /* Ukuran logo */
      height: auto;
    }

    .login-container {
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    .login-container h2 {
      margin-bottom: 20px;
    }

    .login-container .btn-primary {
      background-color: #1565c0;
      border-color: #1565c0;
    }

    .login-container .btn-primary:hover {
      background-color: #0d47a1;
      border-color: #0d47a1;
    }

    .alert {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <!-- Logo di luar form -->
  <img src="{{ asset('logo.png') }}" alt="Logo" class="logo" />

  <div class="login-container">
    <h2 class="text-center mb-4">Login</h2>

    <!-- Error message -->
    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input
          type="email"
          class="form-control"
          id="email"
          name="email"
          placeholder="Enter email"
          required
          autofocus
        />
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input
          type="password"
          class="form-control"
          id="password"
          name="password"
          placeholder="Enter password"
          required
        />
      </div>

      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <div class="mt-3 text-center">
      <a href="">Forgot Your Password ?</a>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle (optional, for components that require JS) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
