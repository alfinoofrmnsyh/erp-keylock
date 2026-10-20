<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
  
    private function formatProduct($product)
    {
        return [
            'id' => $product->id,
            'title' => is_array($product->title) ? $product->title : json_decode($product->title, true),
            'desc'  => is_array($product->desc)  ? $product->desc  : json_decode($product->desc, true),
            'slug'  => $product->slug,
            'trending' => $product->trending,
            'category_slug' => $product->category_slug,
            'image_url' => $product->image ? url('api/image/' . basename($product->image)) : null,
        ];
    }

    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->has('category')) {
            $query->where('category_slug', $request->category);
        }
        $products = $query->get()->map(function ($product) {
            return $this->formatProduct($product);
        });
        return response()->json(['status' => 'success', 'data' => $products]);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->first();

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success', 
            'data' => $this->formatProduct($product)
        ]);
    }
}