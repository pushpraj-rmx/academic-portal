<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Site settings';

    protected static ?string $title = 'Site settings';

    protected static string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $defaults = SiteSetting::defaults();
        $values = SiteSetting::query()->pluck('value', 'key')->toArray();
        $this->data = array_merge($defaults, $values);
    }

    public function form(Form $form): Form
    {
        $defaults = SiteSetting::defaults();
        $components = [];

        $components[] = Section::make('Footer & Ticker')
            ->schema([
                Textarea::make('footer_text')->label('Footer text')->placeholder('Use :year and :name for dynamic values')->rows(2),
                TextInput::make('ticker_label')->label('Ticker label')->maxLength(255),
            ])->columns(1);

        $emptyKeys = array_filter(array_keys($defaults), fn (string $k): bool => str_starts_with($k, 'empty_'));
        $emptyComponents = array_map(
            fn (string $key): TextInput => TextInput::make($key)->label(str_replace('_', ' ', $key))->maxLength(500),
            array_combine($emptyKeys, $emptyKeys)
        );
        $components[] = Section::make('Empty states')->schema(array_values($emptyComponents))->columns(1);

        $backKeys = ['back_to_notices', 'back_to_courses'];
        $backComponents = array_map(
            fn (string $key): TextInput => TextInput::make($key)->label(str_replace('_', ' ', $key))->maxLength(255),
            array_combine($backKeys, $backKeys)
        );
        $components[] = Section::make('Back labels')->schema(array_values($backComponents))->columns(1);

        $sectionKeys = array_filter(array_keys($defaults), fn (string $k): bool => str_starts_with($k, 'section_'));
        $sectionComponents = array_map(
            fn (string $key): TextInput => TextInput::make($key)->label(str_replace('_', ' ', $key))->maxLength(255),
            array_combine($sectionKeys, $sectionKeys)
        );
        $components[] = Section::make('Section titles')->schema(array_values($sectionComponents))->columns(1);

        $components[] = Section::make('Results & Placement')
            ->schema([
                Textarea::make('results_intro')->label('Results intro')->rows(2)->maxLength(500),
                TextInput::make('placement_statistics_link')->label('Placement statistics link text')->maxLength(255),
            ])->columns(1);

        $components[] = Section::make('Currency & units')
            ->schema([
                TextInput::make('currency_symbol')->label('Currency symbol')->maxLength(10),
                TextInput::make('lpa_label')->label('LPA label')->maxLength(20),
            ])->columns(1);

        return $form->schema($components)->statePath('data');
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        foreach ($data as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }
        Notification::make()
            ->title('Site settings saved.')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('page.view') ?? false;
    }
}
