@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">🍣 เพิ่มรีวิวอาหาร</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('member.review.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- 🔹 ร้านอาหาร --}}
                <div class="mb-3">
                    <label for="restaurant_id" class="form-label">เลือกร้านอาหาร</label>
                    <select name="restaurant_id" id="restaurant_id" class="form-select" required>
                        <option value="">-- เลือกร้านอาหาร --</option>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->restaurant_id }}">{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 🔹 เมนู --}}
                <div class="mb-3">
                    <label for="menu_name" class="form-label">ชื่อเมนูอาหาร</label>
                    <input type="text" name="menu_name" id="menu_name" class="form-control" placeholder="เช่น ข้าวมันไก่" required>
                </div>

                {{-- ⭐ ให้คะแนน 5 ดวง (เต็มดวงเท่านั้น) --}}
                <div class="mb-3">
                    <label class="form-label d-block">ให้คะแนน</label>
                    <div class="rating-stars text-center">
                        @for($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" @checked($i==5) required />
                            <label for="star{{ $i }}" title="{{ $i }} ดาว">★</label>
                        @endfor
                    </div>
                </div>

                {{-- 💬 คำอธิบายรีวิว --}}
                <div class="mb-3">
                    <label for="comment" class="form-label">เขียนรีวิวของคุณ</label>
                    <textarea name="comment" id="comment" rows="4" class="form-control" placeholder="พิมพ์รีวิวที่นี่..." required></textarea>
                </div>

                {{-- #️⃣ Hashtags --}}
                <div class="mb-3">
                    <label for="hashtags" class="form-label">Hashtags (เช่น #อร่อย #เผ็ด)</label>
                    <input type="text" name="hashtags" id="hashtags" class="form-control" placeholder="#อร่อย #เผ็ด #คุ้มค่า">
                </div>

                {{-- 🖼️ อัปโหลดรูป --}}
                <div class="mb-3">
                    <label for="image" class="form-label">อัปโหลดรูปเมนู / ร้าน (ถ้ามี)</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>

                {{-- ปุ่มบันทึก --}}
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success px-4">บันทึกรีวิว</button>
                    <a href="{{ route('home.get') }}" class="btn btn-secondary px-4 ms-2">กลับหน้าหลัก</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 💫 CSS สำหรับดาว --}}
<style>
.rating-stars{ display:inline-flex; flex-direction: row-reverse; justify-content:center; }
.rating-stars input{ display:none; }
.rating-stars label{ font-size:2.2rem; color:#ccc; cursor:pointer; padding:0 .15rem; }
.rating-stars label:hover,
.rating-stars label:hover ~ label{ color:#ffc107; }
.rating-stars input:checked ~ label{ color:#ffca08; }
</style>
@endsection
