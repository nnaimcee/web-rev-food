<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // ค่ารวมและสถิติต่าง ๆ บนแดชบอร์ด
        $stats = [
            'users'        => (int) DB::table('users')->count(),
            'restaurants'  => (int) DB::table('restaurants')->count(),
            'menus'        => (int) DB::table('menus')->count(),
            'reviews'      => (int) DB::table('reviews')->count(),
            'likes'        => (int) DB::table('review_likes')->count(),
            'comments'     => (int) DB::table('review_comments')->count(),
            'avg_rating'   => round((float) DB::table('reviews')->avg('rating'), 2),
            'today_reviews'=> (int) DB::table('reviews')->whereDate('created_at', Carbon::today())->count(),
            'week_reviews' => (int) DB::table('reviews')->where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            'today_users'  => (int) DB::table('users')->whereDate('created_at', Carbon::today())->count(),
            'week_users'   => (int) DB::table('users')->where('created_at', '>=', Carbon::now()->subDays(7))->count(),
        ];

        // ร้านยอดนิยม (เรียงตามคะแนนเฉลี่ยและจำนวนรีวิว)
        $topRestaurants = DB::table('reviews')
            ->join('restaurants', 'reviews.restaurant_id', '=', 'restaurants.restaurant_id')
            ->whereNotNull('reviews.restaurant_id')
            ->groupBy('restaurants.restaurant_id', 'restaurants.name')
            ->select(
                'restaurants.restaurant_id',
                'restaurants.name',
                DB::raw('ROUND(AVG(reviews.rating), 2) as avg_rating'),
                DB::raw('COUNT(*) as review_count')
            )
            ->orderByDesc('avg_rating')
            ->orderByDesc('review_count')
            ->limit(5)
            ->get();

        // รีวิวล่าสุด
        $latestReviews = DB::table('reviews')
            ->join('users', 'reviews.user_id', '=', 'users.user_id')
            ->leftJoin('restaurants', 'reviews.restaurant_id', '=', 'restaurants.restaurant_id')
            ->leftJoin('menus', 'reviews.menu_id', '=', 'menus.menu_id')
            ->select(
                'reviews.review_id',
                'reviews.rating',
                'reviews.comment',
                'reviews.created_at',
                'users.username',
                DB::raw('COALESCE(menus.name, reviews.menu_name) as menu_name'),
                'restaurants.name as restaurant_name'
            )
            ->orderByDesc('reviews.created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'topRestaurants', 'latestReviews'));
    }
}
