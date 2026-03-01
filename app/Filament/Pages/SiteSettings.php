<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

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
        // FileUpload expects array; store uses a single path string
        $logoPath = $this->data['logo_path'] ?? null;
        if (is_string($logoPath) && $logoPath !== '') {
            $this->data['logo_path'] = [$logoPath];
        } else {
            $this->data['logo_path'] = [];
        }
        // Repeater expects array of items; decode footer_emails JSON (array of strings) to [['email' => '...'], ...]
        $footerEmailsRaw = $this->data['footer_emails'] ?? '[]';
        $decoded = is_string($footerEmailsRaw) ? json_decode($footerEmailsRaw, true) : $footerEmailsRaw;
        $emails = is_array($decoded) ? $decoded : [];
        $items = [];
        foreach ($emails as $e) {
            if (is_string($e) && $e !== '') {
                $items[] = ['email' => $e];
            }
        }
        $this->data['footer_emails'] = $items ?: [['email' => '']];
    }

    public function form(Form $form): Form
    {
        $defaults = SiteSetting::defaults();
        $components = [];

        $components[] = Section::make('Branding')
            ->schema([
                FileUpload::make('logo_path')
                    ->label('Site logo')
                    ->directory('site')
                    ->disk('public')
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth('200')
                    ->imageResizeTargetHeight('200')
                    ->maxSize(2048)
                    ->nullable()
                    ->helperText('Upload a logo for the header. Recommended: square or wide image, max 2 MB.'),
            ])->columns(1);

        $components[] = Section::make('Header top bar')
            ->schema([
                TextInput::make('topbar_welcome')->label('Welcome text (top black bar)')->placeholder('e.g. Welcome to '.config('app.name'))->maxLength(255),
                TextInput::make('topbar_phone')->label('Call Us (middle bar)')->maxLength(50),
                TextInput::make('topbar_email')->label('Mail Us (middle bar)')->email()->maxLength(255),
                TextInput::make('topbar_location')->label('Location (middle bar)')->maxLength(255),
                TextInput::make('payu_url')->label('PayU / PayUnow URL')->url()->maxLength(500),
            ])->columns(1);

        $components[] = Section::make('Footer – Contact')
            ->schema([
                Textarea::make('footer_address')->label('Address')->rows(3)->maxLength(500),
                TextInput::make('footer_phone')->label('Phone number')->maxLength(50),
                Repeater::make('footer_emails')
                    ->label('Email addresses')
                    ->schema([
                        TextInput::make('email')->label('Email')->email()->maxLength(255),
                    ])
                    ->defaultItems(1)
                    ->addActionLabel('Add email')
                    ->collapsible(),
            ])->columns(1);

        $components[] = Section::make('Footer – Social links')
            ->schema([
                TextInput::make('facebook_url')->label('Facebook URL')->url()->maxLength(500),
                TextInput::make('twitter_url')->label('Twitter URL')->url()->maxLength(500),
                TextInput::make('google_plus_url')->label('Google+ URL')->url()->maxLength(500),
                TextInput::make('linkedin_url')->label('LinkedIn URL')->url()->maxLength(500),
                TextInput::make('pinterest_url')->label('Pinterest URL')->url()->maxLength(500),
                TextInput::make('vimeo_url')->label('Vimeo URL')->url()->maxLength(500),
            ])->columns(1);

        $components[] = Section::make('Footer – Copyright & Ticker')
            ->schema([
                Textarea::make('footer_text')->label('Footer text')->placeholder('Use :year and :name for dynamic values')->rows(2),
                TextInput::make('ticker_label')->label('Ticker label')->maxLength(255),
            ])->columns(1);

        $components[] = Section::make('Testimonials section')
            ->schema([
                TextInput::make('testimonial_section_title')->label('Section title')->maxLength(255),
                Textarea::make('testimonial_section_subtitle')->label('Section subtitle')->rows(2)->maxLength(500),
            ])->columns(1);

        $components[] = Section::make('Homepage sections')
            ->schema([
                TextInput::make('welcome_section_title')->label('Welcome section title')->maxLength(255),
                Textarea::make('welcome_section_body')->label('Welcome section body')->rows(3)->maxLength(2000),
                TextInput::make('stats_section_title')->label('Stats section title')->maxLength(255),
                TextInput::make('certifications_section_title')->label('Certifications section title')->maxLength(255),
                TextInput::make('employers_section_title')->label('Employers section title')->maxLength(255),
            ])->columns(1);

        $components[] = Section::make('Examination center')
            ->schema([
                Textarea::make('examination_center_description')->label('Center description')->rows(3)->maxLength(2000),
                Textarea::make('examination_center_address')->label('Center address')->rows(3)->maxLength(500),
                Textarea::make('examination_center_map_embed')
                    ->label('Map embed HTML')
                    ->rows(3)
                    ->maxLength(5000)
                    ->helperText('Paste iframe embed code for map.'),
            ])->columns(1);

        $components[] = Section::make('Contact page')
            ->schema([
                TextInput::make('contact_page_title')->label('Contact page title')->maxLength(255),
                TextInput::make('contact_page_subtitle')->label('Contact page subtitle')->maxLength(500),
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
        $oldLogoPath = SiteSetting::get('logo_path');

        foreach ($data as $key => $value) {
            if ($key === 'footer_emails') {
                $emails = collect($value)->pluck('email')->filter()->values()->toArray();
                SiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => json_encode($emails)]
                );

                continue;
            }
            $valueToStore = is_array($value) ? ($value[0] ?? null) : $value;
            if ($key === 'logo_path') {
                $valueToStore = $valueToStore ?: null;
                if ($oldLogoPath && (string) $valueToStore !== (string) $oldLogoPath) {
                    Storage::disk('public')->delete($oldLogoPath);
                }
            }
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $valueToStore ?? '']
            );
        }
        Notification::make()
            ->title('Site settings saved.')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return true;
    }
}
