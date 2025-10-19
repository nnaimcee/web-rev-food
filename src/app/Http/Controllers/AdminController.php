<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // ดึงข้อมูลหรือส่งไปหน้า dashboard
        return view('admin.dashboard'); // ✅ ชี้ไปยังไฟล์ resources/views/admin/dashboard.blade.php
    }
}
