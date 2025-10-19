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

        // อนุญาตทั้งผู้ใช้ทั่วไปและแอดมินเข้าเขต member
        if (!Auth::user()->hasAnyRole(['user','admin'])) {
            return redirect()->route('home.get')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return $next($request);
    }
}
