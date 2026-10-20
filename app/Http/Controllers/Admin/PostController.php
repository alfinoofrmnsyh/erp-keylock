<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource; 
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan parameter bahasa, default ke 'id'
        $lang = $request->query('lang', 'id');

        // Mengambil data berdasarkan bahasa
        $posts = Post::where('lang', $lang)
                     ->where('status', 'published')
                     ->get();

        return PostResource::collection($posts);
    }
}