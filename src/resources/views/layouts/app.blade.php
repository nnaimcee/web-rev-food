<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'เว็บรีวิวอาหาร')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @yield('css_before')
</head>
<body class="bg-light d-flex flex-column min-vh-100">

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand fw-bold" href="{{ url('/') }}">FoodReview</a>

      <!-- Hamburger -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
              data-bs-target="#navbarNav" aria-controls="navbarNav"
              aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Search -->
        <form class="d-flex ms-auto me-3 my-2 my-lg-0" method="GET" action="#">
          <input class="form-control me-2" type="search" name="q" placeholder="ค้นหาร้าน/อาหาร..." aria-label="Search">
          <button class="btn btn-outline-light" type="submit">ค้นหา</button>
        </form>

        <ul class="navbar-nav">
          @auth
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu"
                 role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ auth()->user()->avatar_img ? asset('storage/'.auth()->user()->avatar_img) : 'https://via.placeholder.com/40' }}"
                     alt="avatar" class="rounded-circle me-2" width="35" height="35">
                <span>{{ auth()->user()->username }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                @if(auth()->user()->role === 'admin')
                  <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">ไปหลังบ้าน</a></li>
                @endif

                {{-- ใช้ route ของ member edit + ส่ง id ให้ถูกต้อง --}}
                <li>
                  <a class="dropdown-item"
                     href="{{ route('member.memberedit.get', ['id' => auth()->user()->user_id]) }}">
                     แก้ไขโปรไฟล์
                  </a>
                </li>

                <li><hr class="dropdown-divider"></li>
                <li>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">ออกจากระบบ</button>
                  </form>
                </li>
              </ul>
            </li>
          @else
            <li class="nav-item"><a href="{{ route('login.get') }}" class="nav-link">เข้าสู่ระบบ</a></li>
            <li class="nav-item"><a href="{{ route('register.get') }}" class="nav-link">สมัครสมาชิก</a></li>
          @endauth
        </ul>
      </div>
    </div>
  </nav>

  @yield('header')

  <main class="flex-grow-1">
    <div class="container py-4">
      @yield('content')
    </div>
  </main>

  <footer class="bg-dark text-center text-white py-3 mt-auto">
    <div class="container">
      <p class="mb-0">© {{ date('Y') }} by
        <a href="#" class="text-white text-decoration-underline">Tangthai.com</a>
      </p>
    </div>
  </footer>

  @yield('footer')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @yield('js_before')
</body>
</html>
