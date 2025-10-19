<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'FoodReview - Guest')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @yield('css_before')
</head>
<body class="bg-light d-flex flex-column min-vh-100">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
      <a class="navbar-brand fw-bold" href="{{ url('/') }}">FoodReview</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a href="{{ route('login.get') }}" class="nav-link">เข้าสู่ระบบ</a></li>
          <li class="nav-item"><a href="{{ route('register.get') }}" class="nav-link">สมัครสมาชิก</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="flex-grow-1">
    @yield('header')
    <div class="container py-4">
      @yield('content')
    </div>
  </main>

  <footer class="bg-dark text-center text-white py-3 mt-auto">
    <div class="container">
      <p class="mb-0">© {{ date('Y') }} FoodReview</p>
    </div>
  </footer>
  @yield('footer')

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @yield('js_before')
</body>
</html>
