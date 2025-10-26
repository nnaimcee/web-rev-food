<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{
    // ฟอร์มเพิ่มร้านค้า (ต้องล็อกอิน)
    public function create()
    {
        return view('restaurants.create');
    }

    // บันทึกร้านค้าใหม่
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'category'    => 'nullable|string|max:50',
            'location'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('restaurants', 'public');
        }

        DB::table('restaurants')->insert([
            'name'           => trim($validated['name']),
            'restaurant_img' => $imagePath ?? '',
            'category'       => $validated['category'] ?? null,
            'location'       => $validated['location'] ?? null,
            'description'    => $validated['description'] ?? null,
        ]);

        return redirect()->route('home.get')->with('success', 'เพิ่มร้านค้าเรียบร้อยแล้ว');
    }
}

