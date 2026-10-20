<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\{Section, TextInput, Textarea, Select, Tabs, Tabs\Tab};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter; // <-- Import SelectFilter

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationGroup = 'Content Manager';
    protected static ?string $navigationLabel = 'Testimonials';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Klien')->schema([
                TextInput::make('name')
                    ->label('Nama Klien')
                    ->required()
                    ->placeholder('Contoh: Alfino Firmansyah'),

                TextInput::make('role')
                    ->label('Jabatan / Role')
                    ->placeholder('Contoh: IT Consultant'),

                Select::make('rating')
                    ->label('Rating Bintang')
                    ->options([
                        5 => '⭐⭐⭐⭐⭐ (5 Bintang)',
                        4 => '⭐⭐⭐⭐ (4 Bintang)',
                        3 => '⭐⭐⭐ (3 Bintang)',
                        2 => '⭐⭐ (2 Bintang)',
                        1 => '⭐ (1 Bintang)',
                    ])
                    ->default(5)
                    ->required()
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('Pesan Testimoni (Multibahasa)')->schema([
                Tabs::make('Pesan')->tabs([
                    Tab::make('English')->schema([
                        Textarea::make('message.en')
                            ->label('Message (EN)')
                            ->required()
                            ->rows(4),
                    ]),
                    Tab::make('Indonesia')->schema([
                        Textarea::make('message.id')
                            ->label('Pesan (ID)')
                            ->required()
                            ->rows(4),
                    ]),
                    Tab::make('Chinese')->schema([
                        Textarea::make('message.zh-Hans')
                            ->label('Message (ZH)')
                            ->required()
                            ->rows(4),
                    ]),
                ])->columnSpanFull(),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Jabatan')
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->sortable(),

                TextColumn::make('message_en')
                    ->label('Pesan (EN)')
                    ->getStateUsing(fn ($record) => is_array($record->message) ? ($record->message['en'] ?? '-') : '-')
                    ->limit(40)
                    ->searchable(query: function ($query, $search) {
                        return $query->where("message->en", "like", "%{$search}%");
                    }),

                TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // Filter berdasarkan rating bintang
                SelectFilter::make('rating')
                    ->label('Filter Rating')
                    ->options([
                        5 => '5 Bintang (⭐⭐⭐⭐⭐)',
                        4 => '4 Bintang (⭐⭐⭐⭐)',
                        3 => '3 Bintang (⭐⭐⭐)',
                        2 => '2 Bintang (⭐⭐)',
                        1 => '1 Bintang (⭐)',
                    ])
                    ->placeholder('Semua Rating'),
            ])
            ->actions([
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}