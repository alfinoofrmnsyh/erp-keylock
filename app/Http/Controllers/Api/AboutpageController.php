<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aboutpage;
use Illuminate\Http\Request;

class AboutpageController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil parameter locale dari Next.js (?locale=id / en)
        $locale = $request->query('locale', 'id');

        // 2. Ambil baris pertama dari tabel aboutpages
        $about = Aboutpage::first();

        if (!$about) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan di database.'
            ], 404);
        }

        // 3. Pastikan kolom data didecode menjadi Array
        $payload = is_array($about->data) ? $about->data : json_decode($about->data ?? '{}', true);

        // 4. Ambil konten spesifik berdasarkan locale (id / en)
        $content = $payload['content'][$locale] ?? $payload['content']['id'] ?? [];

        // 5. Return JSON bersih dengan struktur status dan data
        return response()->json([
            'status' => 'success',
            'data' => $content
        ]);
    }
}