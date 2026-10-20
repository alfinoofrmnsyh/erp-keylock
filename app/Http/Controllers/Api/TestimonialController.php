<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    private function formatTestimonial($testimonial)
    {
        return [
            'id' => $testimonial->id,
            'name' => $testimonial->name,
            'role' => $testimonial->role,
            'message' => is_array($testimonial->message) ? $testimonial->message : json_decode($testimonial->message, true),
            'rating' => $testimonial->rating,
            'created_at' => $testimonial->created_at,
            'updated_at' => $testimonial->updated_at,
        ];
    }

    public function index(Request $request)
    {
        $testimonials = Testimonial::all()->map(function ($testimonial) {
            return $this->formatTestimonial($testimonial);
        });

        return response()->json([
            'status' => 'success', 
            'data' => $testimonials
        ]);
    }

    public function show($id)
    {
        $testimonial = Testimonial::find($id);

        if (!$testimonial) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Testimoni tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success', 
            'data' => $this->formatTestimonial($testimonial)
        ]);
    }
}