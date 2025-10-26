<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewCommentController;
use App\Http\Controllers\HashtagController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;

/*
|--------------------------------------------------------------------------
| 🌐 Public Routes (หน้าแรก / ทุกคนเข้าถึงได้)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home.get');
Route::get('/home', [HomeController::class, 'index'])->name('home.index');
Route::get('/tags', [HashtagController::class, 'index'])->name('tags.index');

/*
|--------------------------------------------------------------------------
| 🔑 Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'login'])->name('login.get');
// ชั่วคราว: กัน 419 Page Expired ในสภาพแวดล้อมที่ session/CSRF มีปัญหา
Route::post('/post-login', [AuthController::class, 'postLogin'])
    ->name('login.post')
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::get('/register', [AuthController::class, 'register'])->name('register.get');
Route::post('/post-register', [AuthController::class, 'postRegister'])
    ->name('register.post')
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 🛠️ Admin Routes (สำหรับผู้ที่มี role = admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    // 🧑‍⚖️ จัดการผู้ใช้งาน
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::post('/admin/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.updateRole');
    Route::post('/admin/users/{id}/reset-password', [AdminController::class, 'resetUserPassword'])->name('admin.users.resetPassword');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
});

/*
|--------------------------------------------------------------------------
| 👤 Member Routes (สำหรับผู้ใช้ทั่วไป)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'member'])->prefix('member')->name('member.')->group(function () {

    // 🏠 หน้าแรกของสมาชิก
    Route::get('/home', [HomeController::class, 'index'])->name('m_home.get');

    // 🧑‍💻 แก้ไขโปรไฟล์
    Route::get('/profileedit/{id}', [MemberController::class, 'profileEdit'])->name('memberedit.get');
    Route::put('/profileedit/{id}/update', [MemberController::class, 'profileUpdate'])->name('memberupdate.put');

    // 🔒 รีเซ็ตรหัสผ่านสมาชิก
    Route::get('/passwordreset/{id}', [MemberController::class, 'memberReset'])->name('memberpasswordreset.get');
    Route::put('/passwordreset/{id}/reset', [MemberController::class, 'resetMemberPassword'])->name('memberresetupdate.put');
    
    // 🥢 เพิ่มรีวิว
    Route::get('/review/create', [ReviewController::class, 'create'])->name('review.create');
    Route::post('/review/store', [ReviewController::class, 'store'])->name('review.store');

    // 🍣 รายการรีวิวของสมาชิก (ลบ route index ที่ไม่ใช้ เหลือเฉพาะลบ)
    Route::delete('/review/{id}/delete', [ReviewController::class, 'destroy'])->name('review.destroy');

    // 👍🏻 ไลค์รีวิว
    Route::post('/review/{id}/like', [ReviewController::class, 'like'])->name('review.like');
    Route::delete('/review/{id}/unlike', [ReviewController::class, 'unlike'])->name('review.unlike');   
    // ⬇️ โหวตลงรีวิว
    Route::post('/review/{id}/downvote', [ReviewController::class, 'downvote'])->name('review.downvote');
    Route::delete('/review/{id}/undownvote', [ReviewController::class, 'undownvote'])->name('review.undownvote');   

    // ✏️ แก้ไขรีวิว (ปิดใช้งานชั่วคราว)

    // 💬 คอมเมนต์รีวิว
    Route::post('/review/{id}/comment', [ReviewCommentController::class, 'store'])->name('review.comment.store');
    Route::delete('/comment/{id}', [ReviewCommentController::class, 'destroy'])->name('comment.destroy');
});

/*
|--------------------------------------------------------------------------
| 🏪 Restaurant Routes (สมาชิกและแอดมินใช้ได้)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/restaurants/create', [RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('/restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
});
