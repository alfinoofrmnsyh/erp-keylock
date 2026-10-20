<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aboutpage; // Pastikan Model Aboutpage di-import
use Illuminate\Http\Request;

class AboutpageController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil parameter locale, default ke 'en'
        $locale = $request->query('locale', 'en');

        // Mencari data berdasarkan locale
        $about = Aboutpage::where('locale', $locale)->first();

        if (!$about) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Ambil array data dari model
        $data = $about->data;

        // Membersihkan data agar localizations tidak ikut terkirim
        if (isset($data['localizations'])) {
            unset($data['localizations']);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}