<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('frontend/assets/Logo_Algérie_Télécom.png') }}" type="image/png">
  <!-- Bootstrap & FontAwesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Custom Styles -->
  <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}">
  @yield('styles')  <!-- Moved to head section -->

  <style>
    .logo { height: 50px; }
    .profile-pic { width: 40px; height: 40px; border-radius: 50%; }
    .container-custom { max-width: 800px; margin: auto; }
  </style>
</head>

<body class="font-sans antialiased">
  <div class="min-h-screen bg-gray-100">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @isset($header)
      <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          {{ $header }}
        </div>
      </header>
    @endisset

    <!-- Page Content -->
    <main class="py-4">
      @yield('content')
      
    </main>
  </div>

  <!-- Scripts -->
  <script src="{{ asset('frontend/js/script.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
