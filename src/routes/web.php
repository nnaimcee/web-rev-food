<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

// 🏠 Home (หน้าบ้าน ใครก็เข้าได้)
Route::get('/', [HomeController::class, 'index'])->name('home.get');

// 🔑 Auth
Route::get('/login', [AuthController::class, 'login'])->name('login.get');
Route::post('/post-login', [AuthController::class, 'postLogin'])->name('login.post');

Route::get('/register', [AuthController::class, 'register'])->name('register.get');
Route::post('/post-register', [AuthController::class, 'postRegister'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 🛠️ Admin Routes (หลังบ้าน)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    // เพิ่ม route จัดการอื่น ๆ สำหรับ admin ได้ที่นี่
});

// 👤 Member Routes (เฉพาะสมาชิก)
Route::middleware(['auth', 'member'])->prefix('member')->name('member.')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('m_home.get');
    Route::get('/profileedit/{id}', [MemberController::class, 'profileEdit'])->name('memberedit.get');
    Route::put('/profileedit/{id}/update', [MemberController::class, 'profileUpdate'])->name('memberupdate.put');
    Route::get('/passwordreset/{id}', [MemberController::class, 'memberReset'])->name('memberpasswordreset.get');
    Route::put('/passwordreset/{id}/reset', [MemberController::class, 'resetMemberPassword'])->name('memberresetupdate.put');
});
