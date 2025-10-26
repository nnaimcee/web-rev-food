<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewCommentController extends Controller
{
    public function store(Request $request, $reviewId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer',
        ]);

        if (!DB::table('reviews')->where('review_id', $reviewId)->exists()) {
            return redirect()->back()->with('error', 'ไม่พบรีวิวนี้');
        }

        $parentId = $request->input('parent_id');
        if ($parentId) {
            // ตรวจ parent อยู่ในรีวิวเดียวกัน
            $validParent = DB::table('review_comments')
                ->where('comment_id', $parentId)
                ->where('review_id', $reviewId)
                ->exists();
            if (!$validParent) {
                return redirect()->back()->with('error', 'การตอบกลับไม่ถูกต้อง');
            }
        }

        $id = DB::table('review_comments')->insertGetId([
            'review_id' => $reviewId,
            'user_id' => Auth::user()->user_id,
            'parent_id' => $parentId,
            'content' => $request->input('content'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => [
                    'comment_id' => $id,
                    'review_id' => $reviewId,
                    'parent_id' => $parentId,
                    'content' => $request->input('content'),
                    'username' => Auth::user()->username,
                    'avatar_img' => Auth::user()->avatar_img,
                    'role' => Auth::user()->role ?? null,
                    'can_edit' => true,
                    'created_at' => now()->toDateTimeString(),
                ],
            ]);
        }

        return redirect()->back();
    }

    public function destroy($commentId)
    {
        $comment = DB::table('review_comments')->where('comment_id', $commentId)->first();
        if (!$comment) {
            return redirect()->back()->with('error', 'ไม่พบคอมเมนต์');
        }

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login.get');
        }

        // แอดมิน (role = admin) หรือเจ้าของคอมเมนต์ลบได้
        $canDelete = (($user->role ?? null) === 'admin') || ($comment->user_id === $user->user_id);
        if (!$canDelete) {
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์ลบคอมเมนต์นี้');
        }

        DB::table('review_comments')->where('comment_id', $commentId)->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'ลบคอมเมนต์แล้ว');
    }

}
