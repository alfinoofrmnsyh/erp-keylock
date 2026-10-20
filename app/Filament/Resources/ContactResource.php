<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\CmsContact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\{Section, TextInput, FileUpload};
use Filament\Tables\Columns\{TextColumn, ImageColumn};

class ContactResource extends Resource
{
    protected static ?string $model = CmsContact::class;
    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationGroup = 'Content Manager';
    protected static ?string $navigationLabel = 'Contact & QR';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Kontak & WeChat')->schema([
                TextInput::make('contact1')
                    ->label('Contact 1 Marketing & Sales')
                    ->placeholder('Contoh: +62 812-3456-7890'),

                TextInput::make('contact2')
                    ->label('Contact 2 Universal')
                    ->placeholder('Contoh: +62 812-3456-7890'),

                FileUpload::make('wechat_qr')
                    ->label('Foto QR Code WeChat')
                    ->disk('public')
                    ->directory('wechat-qr')
                    ->image()
                    ->required()
                    ->maxSize(2048)
                    ->columnSpanFull(),
            ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('wechat_qr')
                    ->label('QR WeChat')
                    ->square()
                    ->size(50),

                TextColumn::make('contact1')
                    ->label('Contact 1 Marketing & Sales')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('contact2')
                    ->label('Contact 2 Universal')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}