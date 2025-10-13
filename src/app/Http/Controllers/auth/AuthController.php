<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
    public function postregister(Request $request)
    {
        $msg = [
            'username.required' => 'กรุณากรอกข้อมูลชื่อ',
            'username.min' => 'กรุณากรอกข้อมูลชื่อต่ำ :min ตัวอักษร',
            'email.required' => 'กรุณากรอกอีเมล',
            'password.required' => 'กรุณากรอกรหัส',
            'password.min' => 'กรุณากรอกรหัส :min ตัวอักษร',
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
            UserModel::create([
                'username' => strip_tags($request->input('username')),
                'password' => bcrypt($request->password),
                'email'    => strip_tags($request->input('email')),
                'role'     => 'member',
            ]);

            return redirect()->route('login.get')->with('success', 'สมัครสมาชิกเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            // return response()->json(['error' => $e->getMessage()], 500); // debug
            return view('errors.404');
        }
    }

    // login function
    public function postlogin(Request $request)
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

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'member') {
                return redirect()->route('member.m_home.get');
            }

            Auth::logout();
            return redirect()->route('login.get')->withErrors([
                'login' => 'สิทธิ์ไม่ถูกต้อง'
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
