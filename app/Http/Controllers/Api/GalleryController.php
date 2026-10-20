<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    private function formatGallery($gallery)
    {
        return [
            'id' => $gallery->id,
            'title' => is_array($gallery->title) ? $gallery->title : json_decode($gallery->title, true),
            'description' => $gallery->description,
            'category_id' => $gallery->category_id,
            'category_slug' => $gallery->category ? $gallery->category->slug : null,
            'category_name' => $gallery->category && is_array($gallery->category->name) 
                ? $gallery->category->name 
                : ($gallery->category ? json_decode($gallery->category->name, true) : null),
            'image_url' => $gallery->image_url ? url('api/image/' . basename($gallery->image_url)) : null,
            'status' => $gallery->status,
        ];
    }

    public function index(Request $request)
    {
        $query = Gallery::with('category')->where('status', 1);

        // Filter berdasarkan kategori slug jika ada parameter ?category=...
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $galleries = $query->get()->map(function ($gallery) {
            return $this->formatGallery($gallery);
        });

        return response()->json([
            'status' => 'success', 
            'data' => $galleries
        ]);
    }

    public function show($id)
    {
        $gallery = Gallery::with('category')->where('id', $id)->first();

        if (!$gallery) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Galeri tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success', 
            'data' => $this->formatGallery($gallery)
        ]);
    }
}