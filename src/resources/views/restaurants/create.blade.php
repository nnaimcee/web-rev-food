@extends('layouts.app')

@section('title', 'เพิ่มร้านค้า')

@section('content')
  <div class="d-flex align-items-center mb-4">
    <h1 class="h4 mb-0">เพิ่มร้านค้า</h1>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route('restaurants.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label class="form-label">ชื่อร้าน <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">หมวดหมู่</label>
            <input type="text" name="category" class="form-control" value="{{ old('category') }}" placeholder="เช่น ญี่ปุ่น, ไทย, คาเฟ่" />
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">ที่ตั้ง</label>
            <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="เช่น Bangkok" />
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">รายละเอียด</label>
          <textarea name="description" class="form-control" rows="4" placeholder="คำอธิบายร้าน">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">รูปภาพร้าน</label>
          <input type="file" name="image" class="form-control" accept="image/*">
          <div class="form-text">รองรับ jpeg, png, jpg ขนาดไม่เกิน 5MB</div>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">บันทึกร้านค้า</button>
          <a href="{{ route('home.get') }}" class="btn btn-outline-secondary">ยกเลิก</a>
        </div>
      </form>
    </div>
  </div>
@endsection

