<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use function Laravel\Prompts\alert;

class MemberController extends Controller
{

    public function profileEdit(int $id)
    {
        $member = UserModel::findOrFail($id);

        if ((int) Auth::id() !== (int) $member->user_id) {
            return back()->with('error', 'ผู้ใช้ไม่ถูกต้องคุณพยายามแก้ผู้ใช้คนอื่น'); // กลับหน้าเดิมที่ผู้ใช้อยู่ก่อนหน้า
        }

        // ใช้ Facade ให้สอดคล้องกัน
        $layout = Auth::check() ? 'layouts.app' : 'layouts.guest';

        return view('members.edit', compact('member', 'layout'));
    }



    public function profileUpdate(int $id, Request $request)
    {
        $member = UserModel::findOrFail($id);

        if ((int) Auth::id() !== (int) $member->user_id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Forbidden',
                ], 403);
            }
            return back()->with('error', 'คุณไม่มีสิทธิ์แก้ไขโปรไฟล์นี้');
        }

        $msg = [
            'username.required'  => 'กรุณากรอกข้อมูลชื่อ',
            'username.min'       => 'กรุณากรอกชื่ออย่างน้อย :min ตัวอักษร',
            'username.unique'    => 'ชื่อนี้ถูกใช้แล้ว',
            'email.required'     => 'กรุณากรอกอีเมล',
            'email.email'        => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique'       => 'อีเมลนี้ถูกใช้แล้ว',
            'avatar_img.mimes'   => 'รองรับ jpeg, png, jpg เท่านั้น !!',
            'avatar_img.max'     => 'ขนาดไฟล์ไม่เกิน 5MB',
        ];

        $validator = Validator::make($request->all(), [
            'username'   => ['required','string','min:3', Rule::unique('users','username')->ignore($id,'user_id')],
            'email'      => ['required','email',         Rule::unique('users','email')->ignore($id,'user_id')],
            'avatar_img' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ], $msg);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }
            return redirect()
                ->route('member.memberedit.get', ['id' => $id])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = $request->only(['username','email']);

            if ($request->hasFile('avatar_img')) {
                if ($member->avatar_img && Storage::disk('public')->exists($member->avatar_img)) {
                    Storage::disk('public')->delete($member->avatar_img);
                }
                $path = $request->file('avatar_img')->store('user_imgs', 'public');
                $updateData['avatar_img'] = $path;
            }

            $member->update($updateData);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'อัปเดตโปรไฟล์เรียบร้อยแล้ว',
                    'data'    => [
                        'user_id'    => $member->user_id,
                        'username'   => $member->username,
                        'email'      => $member->email,
                        'avatar_img' => $member->avatar_img,
                    ],
                ], 200);
            }

            return redirect()->route('member.m_home.get')->with('success', 'อัปเดตโปรไฟล์เรียบร้อยแล้ว');

        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Internal Server Error',
                ], 500);
            }
            abort(404, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        }
    }

    public function memberReset($id)
    {
        try {
            $member = UserModel::findOrFail($id);

            if ((int) Auth::id() !== (int) $member->user_id) {
            return back()->with('error', 'ผู้ใช้ไม่ถูกต้องคุณพยายามแก้ผู้ใช้คนอื่น'); // กลับหน้าเดิมที่ผู้ใช้อยู่ก่อนหน้า
            }

            // ให้แน่ใจว่ามีไฟล์ view นี้จริง
            return view('member.memberreset', compact('member'));
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Exception $e) {
            // return response()->json(['error' => $e->getMessage()], 500);
            abort(500);
        }
    }

    public function resetMemberPassword(int $id, Request $request)
    {
        $member = UserModel::findOrFail($id);

        // 1) อนุญาตเฉพาะเจ้าของโปรไฟล์เท่านั้น
        if ((int) Auth::id() !== (int) $member->user_id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            return back()->with('error', 'คุณไม่มีสิทธิ์รีเซ็ตรหัสผ่านของผู้ใช้นี้');
        }

        // 2) Validate
        $msg = [
            'password.required'  => 'กรุณากรอกรหัสผ่าน',
            'password.min'       => 'รหัสผ่านต้องมีอย่างน้อย :min ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
        ];

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|confirmed',
        ], $msg);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }
            return redirect()
                ->route('member.memberpasswordreset.get', ['id' => $id])
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // 3) Update
            $member->update(['password' => bcrypt($request->password)]);

            // 4) ตอบกลับตามชนิด request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'รีเซ็ตรหัสผ่านเรียบร้อย',
                    'data' => [
                        'user_id' => $member->user_id,
                        'username'=> $member->username,
                        'email'   => $member->email,
                    ]
                ], 200);
            }

            return redirect()->route('member.m_home.get')->with('success', 'รีเซ็ตรหัสผ่านเรียบร้อย');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Internal Server Error'], 500);
            }
            abort(500, 'ไม่สามารถรีเซ็ตรหัสผ่านได้');
        }
    }
}
