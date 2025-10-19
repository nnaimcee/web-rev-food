<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HashtagController extends Controller
{
    public function index()
    {
        $tags = DB::table('review_hashtags')
            ->select('tag', DB::raw('COUNT(*) as total'))
            ->groupBy('tag')
            ->orderByDesc('total')
            ->paginate(50);

        return view('tags.index', compact('tags'));
    }
}

