<?php
// app/Filament/Widgets/ProductChart.php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProductChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pertumbuhan Produk';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Product::select('category_slug', DB::raw('count(*) as total'))
            ->groupBy('category_slug')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Produk',
                    'data' => $data->pluck('total')->toArray(),
                    'borderColor' => '#f59e0b', // Warna garis (Amber)
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)', // Warna area di bawah garis
                    'fill' => true,
                    'tension' => 0.4, // Membuat garis melengkung (smooth)
                ],
            ],
            'labels' => $data->pluck('category_slug')->map(fn($slug) => strtoupper(str_replace('-', ' ', $slug)))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Diubah menjadi line chart
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1, // Memastikan kenaikan angka adalah 1 (bilangan bulat)
                    ],
                    'suggestedMax' => 5, // Memberikan ruang di atas titik tertinggi agar grafik tidak terlalu menempel ke atas
                ],
            ],
        ];
    }
}