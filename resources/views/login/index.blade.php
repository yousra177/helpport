<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name') }}</title>
  <link rel="icon" href="{{ asset('frontend/assets/Logo_Algérie_Télécom.png') }}" type="image/png">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('frontend/assets/Logo_Algérie_Télécom.svg.png') }}">

  <style>
    body {
      background-color: #f8f9fa;
    }
    .logo {
      height: 50px;
    }
  </style>
</head>
<body class="bg-light">

  <main class="min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow p-4" style="width: 100%; max-width: 450px;">

      <!-- Logo -->
      <div class="text-center mb-3">
        <img src="{{ asset('frontend/assets/Logo_Algérie_Télécom.png') }}" alt="Algérie Télécom Logo" class="logo">
      </div>

      <!-- Session Status -->
      @if (session('status'))
        <div class="alert alert-success text-center small">
          {{ session('status') }}
        </div>
      @endif

      <!-- Form -->
      <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}"
                 class="form-control @error('email') is-invalid @enderror"
                 required autofocus autocomplete="username">
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input id="password" type="password" name="password"
                 class="form-control @error('password') is-invalid @enderror"
                 required autocomplete="current-password">
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-3 form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
          <label class="form-check-label text-sm" for="remember">
            {{ __('Remember me') }}
          </label>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center">
          @if (Route::has('password.request'))
            <a class="text-decoration-none small text-muted" href="{{ route('password.request') }}">
              {{ __('Forgot your password?') }}
            </a>
          @endif

          <button type="submit" class="btn btn-primary">
            {{ __('Log in') }}
          </button>
        </div>
      </form>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
