<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'ERP Operasional';
    protected static ?string $navigationLabel = 'Penawaran & Transaksi';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) Order::where('order_type', 'quotation')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationUrl(): string
    {
        return url('/erp/orders');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Transaksi')->schema([
                Forms\Components\TextInput::make('order_number')
                    ->label('Nomor Dokumen')
                    ->disabled(),

                Forms\Components\Select::make('order_type')
                    ->label('Tipe Transaksi')
                    ->options([
                        'quotation' => 'Quotation (Penawaran Harga)',
                        'rental_order' => 'Rental Order (Kontrak Sewa)',
                        'sales_order' => 'Sales Order (Penjualan Unit)',
                    ])
                    ->required(),

                Forms\Components\DatePicker::make('order_date')
                    ->label('Tanggal Order')
                    ->required(),

                Forms\Components\DatePicker::make('valid_until')
                    ->label('Masa Berlaku Penawaran'),

                Forms\Components\TextInput::make('total_amount')
                    ->label('Total Nilai (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Terkirim ke Klien',
                        'approved' => 'Disetujui',
                        'in_progress' => 'Sedang Berjalan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->label('Syarat & Ketentuan / Catatan')
                    ->columnSpanFull()
                    ->rows(4),
            ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('No. Dokumen')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('contact.name')
                    ->label('Pelanggan / Perusahaan')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Order $record) => $record->contact ? 'PIC: ' . ($record->contact->pic_name ?? '-') : '-'),

                TextColumn::make('order_type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(function (Order $record): string {
                        if ($record->order_type === 'quotation') {
                            return ($record->quotation_type === 'sales') ? 'Penawaran (Jual)' : 'Penawaran (Sewa)';
                        }
                        if ($record->order_type === 'rental_order') return 'Kontrak Sewa (RO)';
                        if ($record->order_type === 'sales_order') return 'Penjualan (SO)';
                        return strtoupper($record->order_type);
                    })
                    ->color(function (Order $record): string {
                        if ($record->order_type === 'quotation') {
                            return ($record->quotation_type === 'sales') ? 'success' : 'warning';
                        }
                        return match ($record->order_type) {
                            'rental_order' => 'info',
                            'sales_order' => 'success',
                            default => 'gray',
                        };
                    }),

                TextColumn::make('order_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total Nilai')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved', 'completed' => 'success',
                        'sent', 'in_progress' => 'info',
                        'draft' => 'gray',
                        'cancelled' => 'danger',
                        default => 'primary',
                    }),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('order_type')
                    ->label('Tipe Dokumen')
                    ->options([
                        'quotation' => 'Penawaran Harga (QTN)',
                        'rental_order' => 'Rental Order (RO)',
                        'sales_order' => 'Sales Order (SO)',
                    ]),

                SelectFilter::make('status')
                    ->label('Status Transaksi')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Terkirim',
                        'approved' => 'Disetujui',
                        'in_progress' => 'Berjalan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Batal',
                    ]),
            ])
            ->actions([
                Action::make('printPdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->url(fn (Order $record): string => route('erp.quotations.pdf', $record->id))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
        ];
    }
}
