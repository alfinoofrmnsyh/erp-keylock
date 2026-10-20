<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil data dari CSV
        $path = storage_path('app/products.csv');
        
        if (!file_exists($path)) {
            $this->command->error("File CSV tidak ditemukan di: {$path}");
            return;
        }

        $csvData = array_map('str_getcsv', file($path));
        $header = array_shift($csvData); 

        $grouped = [];

        // 2. Kelompokkan berdasarkan document_id
        foreach ($csvData as $row) {
            // Pastikan jumlah kolom sesuai
            if (count($header) !== count($row)) continue;

            $data = array_combine($header, $row);
            $docId = $data['document_id'];

            if (!isset($grouped[$docId])) {
                $grouped[$docId] = [
                    'document_id'   => $docId,
                    'slug'          => $data['slug'],
                    'category_slug' => $data['category_slug'],
                    'image'         => $data['image'],
                    'title'         => [],
                    'desc'          => [],
                ];
            }

            // 3. Masukkan ke array sesuai locale
            $locale = $data['locale']; 
            if (!empty($data['title'])) {
                $grouped[$docId]['title'][$locale] = $data['title'];
            }
            if (!empty($data['desc'])) {
                $grouped[$docId]['desc'][$locale] = $data['desc'];
            }
        }

        // 4. Gunakan updateOrCreate agar tidak terjadi error duplikasi
        foreach ($grouped as $docId => $item) {
            Product::updateOrCreate(
                ['document_id' => $docId], // Kunci pencarian
                [
                    'slug'          => $item['slug'] . '-' . $docId,
                    'category_slug' => $item['category_slug'],
                    'image'         => $item['image'],
                    'title'         => $item['title'],
                    'desc'          => $item['desc'],
                ]
            );
        }
        
        $this->command->info("Data berhasil diimpor!");
    }
}