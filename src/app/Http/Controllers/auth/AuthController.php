<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    // login view
    public function login(): View
    {
        return view('auth.login');
    }

    // register view
    public function register(): View
    {
        return view('auth.register');
    }

    // register function
    public function postRegister(Request $request)
    {
        $msg = [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้',
            'username.min' => 'ชื่อผู้ใช้ต้องมีอย่างน้อย :min ตัวอักษร',
            'email.required' => 'กรุณากรอกอีเมล',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย :min ตัวอักษร',
        ];

        $validator = Validator::make($request->all(), [
            'username' => 'required|min:4|unique:users',
            'password' => 'required|min:6',
            'email'    => 'required|email|unique:users',
        ], $msg);

        if ($validator->fails()) {
            return redirect('/register')->withErrors($validator)->withInput();
        }

        try {
            // ✅ สร้าง user ใหม่และเก็บในตัวแปร
            $user = UserModel::create([
                'username' => strip_tags($request->input('username')),
                'password' => Hash::make($request->password),
                'email'    => strip_tags($request->input('email')),
            ]);

            // ✅ กำหนด role 'user' ผ่าน Spatie Permission
            $user->assignRole('user');

            return redirect()->route('login.get')->with('success', 'สมัครสมาชิกเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            // ถ้ามี error ให้ debug ได้โดยเปิดบรรทัดนี้
            // dd($e->getMessage());
            return view('errors.404');
        }
    }

    // login function
    public function postLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'กรุณากรอก Username',
            'password.required' => 'กรุณากรอก Password',
        ]);

        if ($validator->fails()) {
            return redirect('/login')->withErrors($validator)->withInput();
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // 🔹 ตรวจสิทธิ์ตาม Role ของ Spatie
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('user')) {
                return redirect()->route('member.m_home.get');
            }

            Auth::logout();
            return redirect()->route('login.get')->withErrors([
                'login' => 'สิทธิ์ผู้ใช้ไม่ถูกต้อง'
            ]);
        } else {
            return redirect()->route('login.get')->withErrors([
                'login' => 'Username หรือ Password ไม่ถูกต้อง'
            ]);
        }
    }

    // logout function
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.get')->with('success', 'ออกจากระบบเรียบร้อยแล้ว');
    }
}
