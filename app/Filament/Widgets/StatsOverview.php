<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Produk', Product::count())
                ->description('Jumlah produk yang terdaftar')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),
            Stat::make('Kategori', '2') // Sesuaikan dengan logika sistem Anda
                ->description('Kategori produk aktif')
                ->descriptionIcon('heroicon-m-tag'),
        ];
    }
}
