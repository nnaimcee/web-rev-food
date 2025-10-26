@extends('layouts.app')

@section('title', 'จัดการผู้ใช้งาน')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">จัดการผู้ใช้งาน</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">ย้อนกลับแดชบอร์ด</a>
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>ชื่อผู้ใช้</th>
              <th>อีเมล</th>
              <th>สิทธิ์</th>
              <th>สมัครเมื่อ</th>
              <th class="text-end">การจัดการ</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($users as $u)
              <tr>
                <td>{{ $u->user_id }}</td>
                <td>{{ $u->username }}</td>
                <td>{{ $u->email }}</td>
                <td>
                  <form method="POST" action="{{ route('admin.users.updateRole', ['id' => $u->user_id]) }}" class="d-inline-flex gap-2 align-items-center">
                    @csrf
                    <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                      <option value="member" {{ $u->role === 'member' ? 'selected' : '' }}>member</option>
                      <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>admin</option>
                    </select>
                  </form>
                </td>
                <td>{{ \Carbon\Carbon::parse($u->created_at)->format('Y-m-d H:i') }}</td>
                <td class="text-end">
                  <div class="d-inline-flex gap-2">
                    <form method="POST" action="{{ route('admin.users.resetPassword', ['id' => $u->user_id]) }}">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-secondary" onclick="return confirm('ยืนยันรีเซ็ตรหัสผ่านของ {{ $u->username }}? จะตั้งเป็น password123');">รีเซ็ตรหัสผ่าน</button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.destroy', ['id' => $u->user_id]) }}">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('ยืนยันการลบผู้ใช้ {{ $u->username }} ?');">ลบ</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">ไม่พบผู้ใช้งาน</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
