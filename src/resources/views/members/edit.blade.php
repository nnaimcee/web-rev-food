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

                    {{-- 🔸 ฟอร์มต้องยิงไปที่ member.memberupdate.put + ส่ง id และ spoof เป็น PUT --}}
                    <form method="POST"
                          action="{{ route('member.memberupdate.put', ['id' => $member->user_id]) }}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Username -->
                        <div class="mb-3">
                            <label class="form-label">ชื่อผู้ใช้</label>
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   name="username" 
                                   value="{{ old('username', $member->username) }}" 
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
                                   value="{{ old('email', $member->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Avatar -->
                        <div class="mb-3">
                            <label class="form-label">รูปโปรไฟล์</label><br>
                            @if($member->avatar_img)
                                <img src="{{ asset('storage/'.$member->avatar_img) }}" alt="avatar" width="80" class="rounded mb-2">
                            @endif
                            {{-- 🟢 ให้ชื่อฟิลด์ตรงกับ Controller: avatar_img --}}
                            <input type="file" class="form-control @error('avatar_img') is-invalid @enderror" name="avatar_img" accept="image/*">
                            @error('avatar_img')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
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
    {{-- ถ้ามี script JS เพิ่ม --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('flash-alert');
        if (!el) return;
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close(); // ปิดด้วยเอฟเฟกต์ fade
        }, 2000); // 5 วินาที
        });
    </script>
@endsection
