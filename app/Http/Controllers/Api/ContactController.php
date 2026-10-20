<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CmsContact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil parameter locale dari Next.js (?locale=id / en / zh-Hans)
        $locale = $request->query('locale', 'id');

        // 2. Ambil baris pertama dari tabel cms_contacts
        $contact = CmsContact::first();

        if (!$contact) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data kontak tidak ditemukan di database.'
            ], 404);
        }

        // 3. Tentukan kontak aktif berdasarkan bahasa
        // Jika locale adalah bahasa China (zh / zh-Hans), gunakan contact2, jika tidak gunakan contact1
        $selectedContact = (str_starts_with($locale, 'zh')) ? $contact->contact2 : $contact->contact1;

        // 4. Format URL gambar QR WeChat agar bisa langsung diakses oleh Frontend (Next.js)
        $wechatQrUrl = $contact->wechat_qr ? asset('storage/' . $contact->wechat_qr) : null;

        // 5. Susun struktur data yang akan dikirim
        $data = [
            'contact' => $selectedContact,
            'wechat_qr' => $wechatQrUrl,
            // Jika frontend ingin menampilkan kedua kontak secara bersamaan, bisa disertakan juga:
            'contact1' => $contact->contact1,
            'contact2' => $contact->contact2,
        ];

        // 6. Return JSON bersih dengan struktur status dan data
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}