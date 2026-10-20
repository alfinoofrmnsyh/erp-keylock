<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Mengambil daftar produk dengan filter locale & category
     */
    public function index(Request $request)
    {
        try {
            $locale = $request->query('locale', 'en');
            $category = $request->query('category');

            $products = Product::query()
                ->where('locale', $locale)
                ->when($category, fn($q) => $q->where('category_slug', $category))
                ->latest() // Menampilkan yang terbaru lebih dulu
                ->get();

            return response()->json([
                'status' => 'success',
                'meta' => [
                    'total' => $products->count(),
                    'locale' => $locale
                ],
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching products: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal mengambil data'], 500);
        }
    }

    /**
     * Mengambil detail produk berdasarkan slug
     */
    public function show($slug, Request $request)
    {
        $locale = $request->query('locale', 'en');
        
        $product = Product::where('slug', $slug)
                          ->where('locale', $locale)
                          ->first();

        if (!$product) {
            return response()->json([
                'status' => 'error', 
                'message' => "Produk dengan slug '{$slug}' untuk locale '{$locale}' tidak ditemukan"
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }
}