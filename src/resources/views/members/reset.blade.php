@extends('layouts.app')

@section('title', 'รีเซ็ตรหัสผ่าน')

@section('content')
<div class="container py-4" style="max-width: 720px;">
  <h1 class="h4 mb-4">รีเซ็ตรหัสผ่าน</h1>

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
      <form method="POST" action="{{ route('member.memberresetupdate.put', ['id' => $user->user_id]) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label for="password" class="form-label">รหัสผ่านใหม่</label>
          <input type="password" name="password" id="password" class="form-control" required minlength="6">
        </div>

        <div class="mb-3">
          <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่านใหม่</label>
          <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required minlength="6">
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">บันทึก</button>
          <a href="{{ route('member.m_home.get') }}" class="btn btn-outline-secondary">ยกเลิก</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

