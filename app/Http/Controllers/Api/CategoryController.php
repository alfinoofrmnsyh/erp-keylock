<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // Ambil locale dari request, default ke 'id'
        $lang = $request->header('X-Locale', 'id'); 
        
        $categories = Category::all()->map(function ($category) use ($lang) {
            return [
                'slug' => $category->slug,
                'name' => $category->name[$lang] ?? ($category->name['id'] ?? 'N/A'),
                'image_url' => $category->image ? asset('storage/' . $category->image) : null,
            ];
        });

        return response()->json($categories);
    }
}