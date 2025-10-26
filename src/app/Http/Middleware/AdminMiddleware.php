<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // ยังไม่ login
        if (!Auth::check()) {
            return redirect()->route('login.get')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        // ตรวจสอบสิทธิ์จากคอลัมน์ role โดยตรง เพื่อลดการพึ่งพา Spatie
        if (!(Auth::user()->role === 'admin')) {
            return redirect()->route('home.get')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return $next($request);
    }
}
