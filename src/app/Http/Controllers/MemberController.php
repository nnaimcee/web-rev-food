<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function profileEdit(int $id)
    {
        $user = UserModel::findOrFail($id);

        if ((int) Auth::id() !== (int) $user->user_id) {
            return back()->with('error', 'ผู้ใช้ไม่ถูกต้อง คุณพยายามแก้ไขผู้ใช้คนอื่น');
        }

        $layout = Auth::check() ? 'layouts.app' : 'layouts.guest';
        return view('members.edit', compact('user', 'layout'));
    }

    public function profileUpdate(int $id, Request $request)
    {
        $user = UserModel::findOrFail($id);

        if ((int) Auth::id() !== (int) $user->user_id) {
            return back()->with('error', 'คุณไม่มีสิทธิ์แก้ไขโปรไฟล์นี้');
        }

        $msg = [
            'username.required'  => 'กรุณากรอกชื่อผู้ใช้',
            'username.min'       => 'กรุณากรอกชื่ออย่างน้อย :min ตัวอักษร',
            'username.unique'    => 'ชื่อนี้ถูกใช้แล้ว',
            'email.required'     => 'กรุณากรอกอีเมล',
            'email.email'        => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique'       => 'อีเมลนี้ถูกใช้แล้ว',
            'avatar_img.mimes'   => 'รองรับ jpeg, png, jpg เท่านั้น',
            'avatar_img.max'     => 'ขนาดไฟล์ไม่เกิน 5MB',
        ];

        $validator = Validator::make($request->all(), [
            'username'   => ['required','string','min:3', Rule::unique('users','username')->ignore($id,'user_id')],
            'email'      => ['required','email', Rule::unique('users','email')->ignore($id,'user_id')],
            'avatar_img' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'password'   => 'nullable|min:6|confirmed',
        ], $msg);

        if ($validator->fails()) {
            return redirect()
                ->route('member.memberedit.get', ['id' => $id])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = $request->only(['username','email']);

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->input('password'));
            }

            if ($request->hasFile('avatar_img')) {
                if ($user->avatar_img && Storage::disk('public')->exists($user->avatar_img)) {
                    Storage::disk('public')->delete($user->avatar_img);
                }
                $path = $request->file('avatar_img')->store('user_imgs', 'public');
                $updateData['avatar_img'] = $path;
            }

            $user->update($updateData);

            return redirect()->route('member.m_home.get')->with('success', 'อัปเดตโปรไฟล์เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            abort(500, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        }
    }

    public function memberReset($id)
    {
        $user = UserModel::findOrFail($id);

        if ((int) Auth::id() !== (int) $user->user_id) {
            return back()->with('error', 'ผู้ใช้ไม่ถูกต้อง คุณพยายามแก้ไขผู้ใช้คนอื่น');
        }

        // ใช้ view ตามโครงสร้างโฟลเดอร์ที่มีอยู่จริง: resources/views/members/reset.blade.php
        return view('members.reset', compact('user'));
    }

    public function resetMemberPassword(int $id, Request $request)
    {
        $user = UserModel::findOrFail($id);

        if ((int) Auth::id() !== (int) $user->user_id) {
            return back()->with('error', 'คุณไม่มีสิทธิ์รีเซ็ตรหัสผ่านของผู้ใช้นี้');
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|confirmed',
        ], [
            'password.required'  => 'กรุณากรอกรหัสผ่าน',
            'password.min'       => 'รหัสผ่านต้องมีอย่างน้อย :min ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('member.memberpasswordreset.get', ['id' => $id])
                ->withErrors($validator)
                ->withInput();
        }

        $user->update(['password' => bcrypt($request->password)]);
        return redirect()->route('member.m_home.get')->with('success', 'รีเซ็ตรหัสผ่านเรียบร้อย');
    }
}
