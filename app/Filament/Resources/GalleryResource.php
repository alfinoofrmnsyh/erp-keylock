<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use App\Models\Gallery;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\{Section, TextInput, RichEditor, FileUpload, Tabs, Tabs\Tab, Select, Toggle};
use Filament\Tables\Columns\{TextColumn, ImageColumn, IconColumn};
use Filament\Tables\Filters\SelectFilter;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Content Manager';
    protected static ?string $navigationLabel = 'Gallery';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Konten (Multibahasa)')->tabs([
                Tab::make('English')->schema([
                    TextInput::make('title.en')->label('Title (EN)')->required(),
                    RichEditor::make('description.en')->label('Description (EN)'),
                ]),
                Tab::make('Indonesia')->schema([
                    TextInput::make('title.id')->label('Judul (ID)')->required(),
                    RichEditor::make('description.id')->label('Deskripsi (ID)'),
                ]),
                Tab::make('Chinese')->schema([
                    TextInput::make('title.zh-Hans')->label('Title (ZH)')->required(),
                    RichEditor::make('description.zh-Hans')->label('Description (ZH)'),
                ]),
            ])->columnSpanFull(),
                
            Section::make('Pengaturan Galeri')->schema([
                Select::make('category_id')
                    ->label('Kategori')
                    ->options(
                        Category::all()->mapWithKeys(function ($category) {
                            $name = is_array($category->name) ? $category->name : json_decode($category->name, true);
                            return [$category->id => $name['en'] ?? $category->slug];
                        })
                    )
                    ->required()
                    ->searchable(),

                Toggle::make('status')
                    ->label('Status Aktif')
                    ->default(true)
                    ->inline(false),

                FileUpload::make('image_url')
                    ->label('Foto Galeri')
                    ->disk('public')
                    ->directory('galleries')
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
                ImageColumn::make('image_url')->label('Foto')->square()->size(50),
                
                TextColumn::make('title_en')
                    ->label('Judul (EN)')
                    ->getStateUsing(fn ($record) => is_array($record->title) ? ($record->title['en'] ?? '-') : '-')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy("title->en", $direction);
                    })
                    ->searchable(query: function ($query, $search) {
                        return $query->where("title->en", "like", "%{$search}%");
                    }),

                TextColumn::make('category_id')
                    ->label('Kategori')
                    ->formatStateUsing(function ($state) {
                        $category = Category::find($state);
                        if (!$category) {
                            return '-';
                        }
                        
                        $name = $category->name;
                        if (is_array($name)) {
                            return $name['en'] ?? ($name['id'] ?? $category->slug);
                        }
                        
                        $decoded = json_decode($name, true);
                        if (is_array($decoded)) {
                            return $decoded['en'] ?? ($decoded['id'] ?? $category->slug);
                        }
                        
                        return $name ?? $category->slug;
                    })
                    ->badge()
                    ->color('primary'),

                IconColumn::make('status')
                    ->label('Status')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(
                        Category::all()->mapWithKeys(function ($category) {
                            $name = is_array($category->name) ? $category->name : json_decode($category->name, true);
                            return [$category->id => $name['en'] ?? $category->slug];
                        })
                    )
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}