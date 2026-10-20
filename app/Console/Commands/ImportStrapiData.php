<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Homepage;
use App\Models\Aboutpage;
use App\Models\Product;

class ImportStrapiData extends Command
{
    protected $signature = 'import:strapi {file}';
    protected $description = 'Import data JSON dari Strapi (Homepage, About, Product) ke database';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!File::exists($filePath)) {
            $this->error("File tidak ditemukan di: {$filePath}");
            return;
        }

      
        $json = File::get($filePath);
        $content = json_decode($json, true, 512, JSON_UNESCAPED_UNICODE);
        $data = $content;
        // $data = $content['data'];

        // --- HANDLER UNTUK HOMEPAGE ---
        if (str_contains($filePath, 'homepage')) {
            Homepage::updateOrCreate(
                ['locale' => $data['locale']],
                [
                    'hero_section' => $data['hero_section'] ?? null,
                    'about_section' => $data['about_section'] ?? null,
                    'service_section' => $data['service_section'] ?? null,
                    'advantages_section' => $data['advantages_section'] ?? null,
                    'products_section' => $data['products_section'] ?? null,
                    'workflow_section' => $data['workflow_section'] ?? null,
                    'testimonials_section' => $data['testimonials_section'] ?? null,
                    'coverage_section' => $data['coverage_section'] ?? null,
                    'faq_section' => $data['faq_section'] ?? null,
                    'cta_section' => $data['cta_section'] ?? null,
                    'footer_section' => $data['footer_section'] ?? null,
                ]
            );
            $this->info("Homepage ({$data['locale']}) berhasil diimpor!");

        } elseif (str_contains($filePath, 'about')) {
            Aboutpage::updateOrCreate(
                ['id' => 1], // Karena kita sudah sepakat Aboutpage hanya 1 baris
                ['data' => $data] // $data sekarang adalah seluruh struktur JSON Anda
            );
            $this->info("Aboutpage berhasil diimpor!");
        // --- HANDLER UNTUK PRODUCTS ---
        } elseif (str_contains($filePath, 'product')) {
                foreach ($data as $item) {
                    
                    // 1. Bersihkan Deskripsi:
                    // Kita ambil teksnya, lalu hapus semua sisa tanda kutip/backslash agar jadi string murni
                    $cleanDesc = $this->extractText($item['desc']);
                    $cleanDesc = str_replace(['"', '\\'], '', $cleanDesc);

                    // 2. Ambil URL dan bersihkan total (hapus semua tanda kutip & backslash)
                    $rawUrl = $item['image']['url'] ?? null;
                    $cleanImage = $rawUrl ? str_replace(['"', '\\'], '', $rawUrl) : null;

                    // Pastikan tidak ada karakter aneh yang tersisa
                    $cleanImage = trim($cleanImage, ' /'); 

                    Product::updateOrCreate(
                        ['document_id' => $item['documentId'], 'locale' => $item['locale']],
                        [
                            'title'         => $item['title'],
                            'slug'          => $item['slug'],
                            'category_slug' => $item['category_slug'],
                            'desc'          => $cleanDesc, // Sekarang sudah bersih
                            'image'         => $cleanImage, // Sekarang sudah bersih
                        ]
                    );
                }
                $this->info("Semua Produk berhasil diimpor dengan data bersih!");

        } else {
            $this->error("Tipe file tidak dikenali! Pastikan nama file mengandung 'homepage', 'about', atau 'product'.");
        }
    }

    private function extractText($data): string
    {
        if (!is_array($data)) {
            // Ini adalah trik untuk mengubah \uXXXX menjadi karakter asli
            return mb_convert_encoding(json_decode('"' . $data . '"'), 'UTF-8', 'UTF-8');
        }

        $text = "";
        if (isset($data)) {
            foreach ($data as $block) {
                if (isset($block['children']) && is_array($block['children'])) {
                    foreach ($block['children'] as $child) {
                        $rawText = $child['text'] ?? '';
                        // Mengubah \uXXXX menjadi teks Mandarin asli
                        $text .= json_decode('"' . $rawText . '"') . " ";
                    }
                }
            }
        }
        return trim($text);
    }
}