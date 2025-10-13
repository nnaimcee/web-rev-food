<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // 👈 เพิ่มเข้ามา

class HomeController extends Controller
{
    public function index()
    {
        $layout = Auth::check() ? 'layouts.app' : 'layouts.guest';
        return view('home.index', compact('layout'));
    }
}
