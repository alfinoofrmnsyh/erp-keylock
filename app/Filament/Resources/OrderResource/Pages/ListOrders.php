<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('openErpModal')
                ->label('Buat Penawaran di Portal ERP')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('warning')
                ->url(route('erp.index')),
        ];
    }
}
