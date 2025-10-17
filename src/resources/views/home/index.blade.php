@extends($layout)

@section('title', 'เว็บรีวิวอาหาร')

@section('css_before')
<style>
  /* ปุ่มลอยมุมขวาล่าง */
  .fab-post {
    position: fixed;
    right: 20px;
    bottom: 80px;
    z-index: 1030;
  }
  .review-cover {
    height: 180px;
    object-fit: cover;
  }
</style>
@endsection

@section('header')
<div class="bg-light text-black py-5">
  <div class="container">
    <div class="row align-items-center g-3">
      <div class="col-lg-8">
        <h1 class="fw-bold mb-2">ค้นหาและแชร์รีวิวอาหารอร่อย 🍜</h1>
        <p class="mb-4">บอกต่อร้านเด็ด เมนูโปรด พร้อมรูปอาหารน่ากินได้ที่นี่</p>
        <form class="row g-2" method="GET" action="#">
          <div class="col-md-8">
            <input type="search" name="q" class="form-control" placeholder="ค้นหาร้าน / เมนู / ทำเล">
          </div>
          <div class="col-md-4">
            <button class="btn btn-primary w-100" type="submit">ค้นหา</button>
          </div>
        </form>
      </div>
      <div class="col-lg-4 text-lg-end">
        <div class="btn-group" role="group" aria-label="Filters">
          <a href="#" class="btn btn-outline-dark">ทั้งหมด</a>
          <a href="#" class="btn btn-outline-dark">อาหารไทย</a>
          <a href="#" class="btn btn-outline-dark">คาเฟ่</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="container py-4">
  {{-- Flash message --}}
  @foreach (['success','error'] as $t)
    @if(session($t))
      <div class="alert alert-{{ $t === 'error' ? 'danger' : 'success' }} alert-dismissible fade show" role="alert" data-auto-dismiss="5000">
        {{ session($t) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif
  @endforeach

  {{-- ตัวอย่างการ์ดรีวิว (mockup) --}}
  <div class="row g-4">
    @for ($i = 1; $i <= 6; $i++)
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <img class="card-img-top review-cover" src="https://picsum.photos/seed/food{{ $i }}/600/400" alt="cover">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title mb-1">ร้านอร่อย #{{ $i }}</h5>
            <p class="text-muted small mb-2">สยาม • อาหารไทย • ฿฿</p>
            <p class="card-text flex-grow-1">รสชาติจัดจ้าน กลมกล่อม บรรยากาศดี เหมาะนั่งยาว ๆ</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="text-warning">
                ★★★★☆
              </div>
              <a href="#" class="btn btn-outline-primary btn-sm">อ่านรีวิว</a>
            </div>
          </div>
        </div>
      </div>
    @endfor
  </div>
</div>

{{-- ปุ่มลอย: เขียนรีวิว --}}
@auth
  {{-- ถ้าล็อกอิน ให้ไปหน้าเขียนรีวิว --}}
  <a href="#" class="btn btn-primary btn-lg rounded-pill fab-post">
    ✍️ เขียนรีวิว
  </a>
@else
  {{-- ถ้าไม่ล็อกอิน ให้เด้ง modal --}}
  <button type="button" class="btn btn-primary btn-lg rounded-pill fab-post" data-bs-toggle="modal" data-bs-target="#loginRequiredModal">
    ✍️ เขียนรีวิว
  </button>
@endauth

{{-- Modal แจ้งให้ล็อกอินก่อน --}}
<div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginRequiredLabel">ต้องเข้าสู่ระบบก่อน</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
      </div>
      <div class="modal-body">
        เพื่อเขียนรีวิว กรุณาเข้าสู่ระบบหรือสมัครสมาชิกก่อนนะครับ 😊
      </div>
      <div class="modal-footer">
        <a href="{{ route('login.get') }}" class="btn btn-primary">เข้าสู่ระบบ</a>
        <a href="{{ route('register.get') }}" class="btn btn-outline-primary">สมัครสมาชิก</a>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js_before')
<script>
  // auto dismiss alerts
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert[data-auto-dismiss]').forEach(el => {
      const ms = parseInt(el.getAttribute('data-auto-dismiss'), 10) || 5000;
      setTimeout(() => bootstrap.Alert.getOrCreateInstance(el).close(), ms);
    });
  });
</script>
@endsection
