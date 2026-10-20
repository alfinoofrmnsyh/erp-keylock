<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\{Section, TextInput, RichEditor, FileUpload, Tabs, Tabs\Tab, Select};
use Filament\Tables\Columns\{TextColumn, ImageColumn};
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Str;
use Filament\Tables\Filters\SelectFilter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Content Manager';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Konten (Multibahasa)')->tabs([
                Tab::make('English')->schema([
                    TextInput::make('title.en')->label('Title (EN)')->required(),
                    RichEditor::make('desc.en')->label('Description (EN)'),
                ]),
                Tab::make('Indonesia')->schema([
                    TextInput::make('title.id')->label('Judul (ID)'),
                    RichEditor::make('desc.id')->label('Deskripsi (ID)'),
                ]),
                Tab::make('Chinese')->schema([
                    TextInput::make('title.zh-Hans')->label('Title (ZH)'),
                    RichEditor::make('desc.zh-Hans')->label('Description (ZH)'),
                ]),
            ])->columnSpanFull(),
                
            Section::make('Pengaturan Produk')->schema([
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull()
                    ->suffixAction(
                        Action::make('generateSlug')
                            ->icon('heroicon-m-clipboard-document-check')
                            ->action(fn ($get, $set) => $set('slug', Str::slug($get('title.en'))))
                    ),
                
                Select::make('category_slug')
                    ->label('Kategori')
                    ->options(
                        Category::all()->mapWithKeys(function ($category) {
                            $name = is_array($category->name) ? $category->name : json_decode($category->name, true);
                            return [$category->slug => $name['en'] ?? $category->slug];
                        })
                    )
                    ->required()
                    ->searchable(),

                FileUpload::make('image')
                    ->disk('public')
                    ->directory('products')
                    ->image()
                    ->maxSize(2048),
            ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('Gambar')->square()->size(50),
                
                TextColumn::make('title_en')
                    ->label('Judul (EN)')
                    ->getStateUsing(fn ($record) => is_array($record->title) ? ($record->title['en'] ?? '-') : '-')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy("title->en", $direction);
                    })
                    ->searchable(query: function ($query, $search) {
                        return $query->where("title->en", "like", "%{$search}%");
                    }),
                
                TextColumn::make('category_slug')
                    ->label('Kategori')
                    ->formatStateUsing(function ($state) {
                        static $categoryMap = null;
                        if ($categoryMap === null) {
                            $categoryMap = Category::all()->keyBy('slug');
                        }

                        $category = $categoryMap->get($state);
                        if (!$category) {
                            return $state;
                        }
                        
                        $name = $category->name;
                        if (is_array($name)) {
                            return $name['en'] ?? ($name['id'] ?? $state);
                        }
                        
                        $decoded = is_string($name) ? json_decode($name, true) : null;
                        if (is_array($decoded)) {
                            return $decoded['en'] ?? ($decoded['id'] ?? $state);
                        }
                        
                        return $name ?? $state;
                    })
                    ->badge()
                    ->color('primary'),

                TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_slug')
                    ->label('Kategori')
                    ->options(
                        Category::all()->mapWithKeys(function ($category) {
                            $name = is_array($category->name) ? $category->name : json_decode($category->name, true);
                            return [$category->slug => $name['en'] ?? $category->slug];
                        })
                    )
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}