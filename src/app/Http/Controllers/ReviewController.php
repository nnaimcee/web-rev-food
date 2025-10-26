<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Restaurant;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReviewController extends Controller
{
    // 🥢 แสดงฟอร์มเพิ่มรีวิว
    public function create()
    {
        $restaurants = Restaurant::all();
        return view('reviews.create', compact('restaurants'));
    }

    // 🍣 บันทึกข้อมูลรีวิวใหม่ลงฐานข้อมูล
    public function store(Request $request)
    {
        // แยกกรณีเลือกร้านจากรายการ หรือเพิ่มร้านใหม่
        $isNewRestaurant = $request->input('restaurant_id') === 'new';

        // ตรวจสอบข้อมูลตามกรณี
        $rules = [
            'menu_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'hashtags' => 'nullable|string|max:255',
        ];
        if ($isNewRestaurant) {
            $rules['new_restaurant_name'] = 'required|string|max:100';
        } else {
            $rules['restaurant_id'] = 'required|exists:restaurants,restaurant_id';
        }
        $request->validate($rules);

        // จัดการรูปของ review
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reviews', 'public');
        }

        // หา/สร้าง restaurant_id ตามที่เลือก
        if ($isNewRestaurant) {
            $restaurantId = DB::table('restaurants')->insertGetId([
                'name'           => trim($request->input('new_restaurant_name')),
                'restaurant_img' => '',
                'category'       => null,
                'location'       => null,
                'description'    => null,
            ]);
        } else {
            $restaurantId = (int) $request->input('restaurant_id');
        }

        // บันทึกลง reviews เท่านั้น ไม่สร้างเมนูใหม่
        $review = Review::create([
            'user_id' => Auth::user()->user_id,
            'restaurant_id' => $restaurantId,
            'menu_id' => null,
            'menu_name' => trim($request->menu_name),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'image_path' => $imagePath,
        ]);

        // บันทึก hashtags (#tag1 #tag2) — อนุญาตสระ/วรรณยุกต์ (รวม combining marks) ในแท็กได้
        $raw = (string) $request->input('hashtags', '');
        if ($raw !== '') {
            // รวมอักษร + combining marks เพื่อให้สระไทย/วรรณยุกต์ติดไปกับแท็กได้
            preg_match_all('/#([\p{L}\p{M}0-9_]+)/u', $raw, $m);
            $tags = collect($m[1] ?? [])->map(function($t){
                return trim($t);
            })->filter()->unique()->take(10); // จำกัด 10 แท็ก
            if ($tags->isNotEmpty()) {
                DB::table('review_hashtags')->insert($tags->map(function($t) use ($review) {
                    return [ 'review_id' => $review->review_id, 'tag' => $t ];
                })->values()->all());
            }
        }

        return redirect()->route('home.get')->with('success', 'รีวิวของคุณถูกบันทึกเรียบร้อยแล้ว!');
    }

    // 👍🏻 กดถูกใจรีวิว (user ละ 1 ครั้ง)
    public function like($id)
    {
        if (!Auth::check()) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'unauthenticated'], 401)
                : redirect()->route('login.get');
        }

        $userId = Auth::user()->user_id;

        // ตรวจว่ามีรีวิวอยู่จริง
        $exists = DB::table('reviews')->where('review_id', $id)->exists();
        if (!$exists) {
            return redirect()->back()->with('error', 'ไม่พบรีวิวนี้');
        }

        // กันการไลค์ซ้ำ
        $already = DB::table('review_likes')
            ->where('review_id', $id)
            ->where('user_id', $userId)
            ->exists();

        if (!$already) {
            DB::table('review_likes')->insert([
                'review_id' => $id,
                'user_id' => $userId,
            ]);
        }

        if (request()->expectsJson()) {
            $up = DB::table('review_likes')->where('review_id', $id)->count();
            $down = Schema::hasTable('review_downvotes') ? DB::table('review_downvotes')->where('review_id', $id)->count() : 0;
            return response()->json(['success' => true, 'liked' => true, 'count' => ($up - $down), 'up' => $up, 'down' => $down]);
        }
        return redirect()->back();
    }

    // 💔 ยกเลิกถูกใจรีวิว
    public function unlike($id)
    {
        if (!Auth::check()) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'unauthenticated'], 401)
                : redirect()->route('login.get');
        }

        $userId = Auth::user()->user_id;
        DB::table('review_likes')
            ->where('review_id', $id)
            ->where('user_id', $userId)
            ->delete();

        if (request()->expectsJson()) {
            $up = DB::table('review_likes')->where('review_id', $id)->count();
            $down = Schema::hasTable('review_downvotes') ? DB::table('review_downvotes')->where('review_id', $id)->count() : 0;
            return response()->json(['success' => true, 'liked' => false, 'count' => ($up - $down), 'up' => $up, 'down' => $down]);
        }
        return redirect()->back();
    }

    // ⬇️ โหวตลงรีวิว
    public function downvote($id)
    {
        if (!Auth::check()) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'unauthenticated'], 401)
                : redirect()->route('login.get');
        }

        $userId = Auth::user()->user_id;
        $exists = DB::table('reviews')->where('review_id', $id)->exists();
        if (!$exists) {
            return redirect()->back()->with('error', 'ไม่พบรีวิวนี้');
        }

        if (!Schema::hasTable('review_downvotes')) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'feature_not_migrated'], 503)
                : redirect()->back()->with('error', 'ฟีเจอร์โหวตลงยังไม่พร้อมใช้งาน (ยังไม่ได้ migrate)');
        }

        $already = DB::table('review_downvotes')
            ->where('review_id', $id)
            ->where('user_id', $userId)
            ->exists();

        if (!$already) {
            DB::table('review_downvotes')->insert([
                'review_id' => $id,
                'user_id' => $userId,
            ]);
        }

        if (request()->expectsJson()) {
            $up = DB::table('review_likes')->where('review_id', $id)->count();
            $down = DB::table('review_downvotes')->where('review_id', $id)->count();
            return response()->json(['success' => true, 'downvoted' => true, 'count' => ($up - $down), 'up' => $up, 'down' => $down]);
        }
        return redirect()->back();
    }

    // ❌ ยกเลิกโหวตลง
    public function undownvote($id)
    {
        if (!Auth::check()) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'unauthenticated'], 401)
                : redirect()->route('login.get');
        }

        $userId = Auth::user()->user_id;
        if (!Schema::hasTable('review_downvotes')) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'feature_not_migrated'], 503)
                : redirect()->back()->with('error', 'ฟีเจอร์โหวตลงยังไม่พร้อมใช้งาน (ยังไม่ได้ migrate)');
        }
        DB::table('review_downvotes')
            ->where('review_id', $id)
            ->where('user_id', $userId)
            ->delete();

        if (request()->expectsJson()) {
            $up = DB::table('review_likes')->where('review_id', $id)->count();
            $down = DB::table('review_downvotes')->where('review_id', $id)->count();
            return response()->json(['success' => true, 'downvoted' => false, 'count' => ($up - $down), 'up' => $up, 'down' => $down]);
        }
        return redirect()->back();
    }

    // 🗑️ ลบรีวิว (เจ้าของหรือแอดมิน)
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login.get');
        }

        $review = DB::table('reviews')->where('review_id', $id)->first();
        if (!$review) {
            return redirect()->back()->with('error', 'ไม่พบรีวิวนี้');
        }

        // อนุญาตให้เจ้าของหรือผู้ที่มี role = admin ลบได้
        if ($review->user_id !== $user->user_id && ($user->role ?? null) !== 'admin') {
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์ลบรีวิวนี้');
        }

        DB::table('reviews')->where('review_id', $id)->delete();
        return redirect()->back()->with('success', 'ลบรีวิวแล้ว');
    }
}
