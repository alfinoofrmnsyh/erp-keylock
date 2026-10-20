<?php

namespace App\Filament\Pages;

use App\Models\Aboutpage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;

class ManageAboutPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static string $view = 'filament.pages.manage-about-page';
    protected static ?string $title = 'About Page';
    protected static ?string $navigationGroup = 'Content Manager';

    public ?array $data = [];

    public function mount(): void
    {
        $record = Aboutpage::first();

        if ($record) {
            $this->form->fill($record->data);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Bahasa')
                    ->tabs([
                        $this->getTabSchema('Indonesia', 'id'),
                        $this->getTabSchema('English', 'en'),
                        $this->getTabSchema('Chinese', 'zh-Hans'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getTabSchema(string $label, string $lang): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make($label)
            ->schema([

                Forms\Components\Section::make('Header & Statistik')
                    ->schema([

                        Forms\Components\TextInput::make("content.$lang.badge")
                            ->label('Badge'),

                        Forms\Components\TextInput::make("content.$lang.title")
                            ->label('Title'),

                        Forms\Components\Grid::make(2)
                            ->schema([

                                Forms\Components\TextInput::make("content.$lang.stats_units_value")
                                    ->label('Units Value'),

                                Forms\Components\TextInput::make("content.$lang.stats_units_label")
                                    ->label('Units Label'),

                                Forms\Components\TextInput::make("content.$lang.stats_cities_value")
                                    ->label('Cities Value'),

                                Forms\Components\TextInput::make("content.$lang.stats_cities_label")
                                    ->label('Cities Label'),

                                Forms\Components\TextInput::make("content.$lang.stats_clients_value")
                                    ->label('Clients Value'),

                                Forms\Components\TextInput::make("content.$lang.stats_clients_label")
                                    ->label('Clients Label'),
                            ]),
                    ]),

                Forms\Components\Section::make('Visi & Misi')
                    ->schema([

                        Forms\Components\TextInput::make("content.$lang.vision_eyebrow")
                            ->label('Vision Eyebrow'),

                        Forms\Components\TextInput::make("content.$lang.vision_title")
                            ->label('Vision Title'),

                        Forms\Components\RichEditor::make("content.$lang.vision_text")
                            ->label('Vision Text'),

                        Forms\Components\TextInput::make("content.$lang.mission_eyebrow")
                            ->label('Mission Eyebrow'),

                        Forms\Components\TextInput::make("content.$lang.mission_title")
                            ->label('Mission Title'),
                    ]),

                Forms\Components\Section::make('Deskripsi')
                    ->schema([

                        Forms\Components\RichEditor::make("content.$lang.description_1")
                            ->label('Description 1'),

                        Forms\Components\RichEditor::make("content.$lang.description_2")
                            ->label('Description 2'),

                        Forms\Components\Repeater::make("content.$lang.mission_list")
                            ->label('Mission List')
                            ->schema([
                                Forms\Components\TextInput::make('text')
                                    ->label('Mission'),
                            ])
                            ->defaultItems(0)
                            ->columns(1),
                    ]),
            ]);
    }


    public function save(): void
    {
        $state = $this->form->getState();
        
        // Cari record, atau buat jika belum ada
        $about = Aboutpage::first() ?? new Aboutpage();
        
        // Simpan state form ke dalam kolom 'data'
        $about->data = $state;
        $about->save();

        Notification::make()
            ->title('Data berhasil disimpan!')
            ->success()
            ->send();
    }
}