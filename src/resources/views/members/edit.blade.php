@extends('layouts.app')

@section('title', 'แก้ไขโปรไฟล์')

@section('css_before')
    {{-- ถ้ามี CSS พิเศษใส่ที่นี่ --}}
@endsection

@section('header')
    <div class="container mt-4">
        <h2 class="fw-bold">แก้ไขโปรไฟล์</h2>
    </div>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    @if(session('error'))
                        <div id="flash-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- ✅ ใช้ตัวแปร $user (ตรงกับ MemberController) และ route ที่ Laravel มองเห็นแน่นอน --}}
                    <form method="POST"
                          action="{{ route('member.memberupdate.put', ['id' => $user->user_id]) }}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- ใช้ spoof method เพื่อให้ตรงกับ Route::put() --}}

                        <!-- Username -->
                        <div class="mb-3">
                            <label class="form-label">ชื่อผู้ใช้</label>
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   name="username" 
                                   value="{{ old('username', $user->username) }}" 
                                   required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">อีเมล</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Avatar -->
                        <div class="mb-3">
                            <label class="form-label">รูปโปรไฟล์</label><br>
                            @if($user->avatar_img)
                                <img src="{{ asset('storage/'.$user->avatar_img) }}" alt="avatar" width="80" class="rounded mb-2 border">
                            @else
                                <img src="https://cdn-icons-png.freepik.com/512/11121/11121549.png" alt="default avatar" width="80" class="rounded mb-2 border">
                            @endif
                            <input type="file" 
                                   class="form-control @error('avatar_img') is-invalid @enderror" 
                                   name="avatar_img" 
                                   accept="image/*">
                            @error('avatar_img')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>
                        <div class="mb-2 fw-semibold">เปลี่ยนรหัสผ่าน (ไม่บังคับ)</div>
                        <div class="mb-3">
                            <label class="form-label" for="password">รหัสผ่านใหม่</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="ปล่อยว่างหากไม่ต้องการเปลี่ยน">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password_confirmation">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="ยืนยันรหัสผ่านใหม่">
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary w-100">บันทึกการเปลี่ยนแปลง</button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('footer')
    {{-- ถ้ามี footer เพิ่มเติม --}}
@endsection

@section('js_before')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('flash-alert');
    if (!el) return;
    setTimeout(() => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
        bsAlert.close();
    }, 3000);
});
</script>
@endsection
