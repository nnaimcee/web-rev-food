<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        $layout = Auth::check() ? 'layouts.app' : 'layouts.guest';

        // ดึงข้อมูลรีวิวพร้อมข้อมูลร้าน อาหาร ผู้เขียน และจำนวนไลค์ + ทำหน้าเพจ
        $query = DB::table('reviews')
            ->leftJoin('restaurants', 'reviews.restaurant_id', '=', 'restaurants.restaurant_id')
            ->leftJoin('menus', 'reviews.menu_id', '=', 'menus.menu_id')
            ->join('users', 'reviews.user_id', '=', 'users.user_id')
            ->select(
                'reviews.*',
                'restaurants.name as restaurant_name',
                DB::raw('COALESCE(menus.name, reviews.menu_name) as menu_name'),
                'users.username',
                DB::raw('(SELECT COUNT(*) FROM review_likes WHERE review_likes.review_id = reviews.review_id) as like_count')
            );

        // ค้นหา
        if (request()->filled('q')) {
            $q = '%' . trim(request('q')) . '%';
            $query->where(function($w) use ($q) {
                $w->where('restaurants.name', 'like', $q)
                  ->orWhere('menus.name', 'like', $q)
                  ->orWhere('reviews.menu_name', 'like', $q);
            });
        }

        // กรองตามร้านอาหาร
        if (request()->filled('restaurant')) {
            $rid = (int) request('restaurant');
            if ($rid > 0) {
                $query->where('reviews.restaurant_id', $rid);
            }
        }

        // กรองตาม hashtag
        if (request()->filled('tag')) {
            $tag = trim(request('tag'));
            $query->whereExists(function($sub) use ($tag) {
                $sub->from('review_hashtags')
                    ->whereColumn('review_hashtags.review_id', 'reviews.review_id')
                    ->where('review_hashtags.tag', $tag);
            });
        }

        $reviews = $query->orderByDesc('reviews.created_at')->paginate(12);

        // รวบรวมรีวิวที่ผู้ใช้คนปัจจุบันกดถูกใจไว้ (ในหน้าเพจนี้)
        $likedReviewIds = [];
        if (auth()->check()) {
            $ids = collect($reviews->items())->pluck('review_id');
            if ($ids->isNotEmpty()) {
                $likedReviewIds = DB::table('review_likes')
                    ->where('user_id', auth()->user()->user_id)
                    ->whereIn('review_id', $ids)
                    ->pluck('review_id')
                    ->toArray();
            }
        }

        // ดึง hashtag ของรีวิวในหน้า
        $tagsMap = [];
        $ids = collect($reviews->items())->pluck('review_id');
        if ($ids->isNotEmpty()) {
            $tags = DB::table('review_hashtags')
                ->whereIn('review_id', $ids)
                ->orderBy('tag')
                ->get();
            foreach ($tags as $t) {
                $tagsMap[$t->review_id][] = $t->tag;
            }
        }

        // ดึงคอมเมนต์ทั้งหมดของรีวิวในหน้า พร้อมผู้เขียน
        $commentsByReview = [];
        if ($ids->isNotEmpty()) {
            $comments = DB::table('review_comments')
                ->join('users', 'review_comments.user_id', '=', 'users.user_id')
                ->select('review_comments.*', 'users.username')
                ->whereIn('review_comments.review_id', $ids)
                ->orderBy('review_comments.created_at')
                ->get();
            foreach ($comments as $c) {
                $commentsByReview[$c->review_id][] = $c;
            }
        }

        return view('home.index', compact('layout', 'reviews', 'likedReviewIds', 'tagsMap', 'commentsByReview'));
    }
}
