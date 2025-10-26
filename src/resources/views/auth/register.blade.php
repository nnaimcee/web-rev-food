@extends('layouts.guest')

@section('title', 'สมัครสมาชิก')

@section('content')
  <div class="d-flex align-items-center justify-content-center">
    <div class="card shadow-lg p-4" style="max-width: 500px; width: 100%;">
      <h3 class="text-center mb-4">สมัครสมาชิก</h3>

      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <form method="POST" action="{{ route('register.post') }}">
        @csrf

        <div class="mb-3">
          <label for="username" class="form-label">ชื่อผู้ใช้</label>
          <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
          @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">อีเมล</label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">รหัสผ่าน</label>
          <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
          @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่าน</label>
          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-success">สมัครสมาชิก</button>
        </div>

        <div class="mt-3 text-center">
          มีบัญชีแล้ว? <a href="{{ route('login.get') }}">เข้าสู่ระบบ</a>
        </div>
      </form>
    </div>
  </div>
@endsection
