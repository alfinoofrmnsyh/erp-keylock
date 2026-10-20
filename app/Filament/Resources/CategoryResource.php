<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Components\Actions\Action; 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Tabs; 
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;


class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // app/Filament/Resources/CategoryResource.php

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Tabs::make('Nama Kategori')
                ->tabs([
                    Tab::make('Bahasa Indonesia')
                        ->schema([
                            TextInput::make('name.id')->label('Nama (ID)')->required(),
                        ]),
                    Tab::make('English')
                        ->schema([
                            TextInput::make('name.en')->label('Name (EN)'),
                        ]),
                    Tab::make('Chinese')
                        ->schema([
                            TextInput::make('name.zh-Hans')->label('Name (ZH)'),
                        ]),
                ])
                ->columnSpanFull(),

            TextInput::make('slug')
            ->required()
            ->unique(ignoreRecord: true)
            ->suffixAction(
             Action::make('generate-slug')
            ->icon('heroicon-m-clipboard-document-check')
            ->label('Generate')
            ->visible(fn ($state) => empty($state)) // Tombol hanya muncul jika slug kosong
            ->action(function ($set, $get) {
                $name = $get('name.en');
                if ($name) {
                    $set('slug', Str::slug($name));
                }
            })
        ),
        FileUpload::make('image')
            ->label('Gambar Kategori')
            ->image()
            ->directory('categories') 
            ->imageEditor() 
            ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                ->label('Gambar')
                ->square()
                ->size(50),

            Tables\Columns\TextColumn::make('name_en')
                ->label('Nama')
                ->getStateUsing(fn ($record) => $record->name['en'] ?? '-')
                ->searchable(),

            Tables\Columns\TextColumn::make('slug')
                ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
