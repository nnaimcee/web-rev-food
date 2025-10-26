<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class MemberMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login.get')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        // อนุญาตทั้งผู้ใช้ทั่วไป (member) และแอดมินเข้าเขต member โดยเช็คจากคอลัมน์ role
        $role = Auth::user()->role ?? null;
        if (!in_array($role, ['member','admin'], true)) {
            return redirect()->route('home.get')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return $next($request);
    }
}
