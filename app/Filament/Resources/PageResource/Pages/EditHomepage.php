<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\Page as PageModel;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;

class EditHomepage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = PageResource::class;

    protected static string $view = 'filament.resources.page-resource.pages.edit-homepage';

    protected static ?string $title = 'Edit Homepage';

    public ?array $data = [];

    public function mount(): void
    {
        $page = PageModel::firstOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'body' => '',
                'meta_description' => '',
                'is_published' => true,
                'sort_order' => 0,
            ]
        );

        $defaults = SiteSetting::defaults();
        $keys = [
            'welcome_section_title',
            'welcome_section_body',
            'stats_section_title',
            'certifications_section_title',
            'employers_section_title',
            'testimonial_section_title',
            'testimonial_section_subtitle',
            'two_column_image',
            'two_column_image_alt',
            'two_column_title',
            'two_column_body',
            'two_column_image_first',
        ];
        $settings = SiteSetting::query()
            ->whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();

        $twoColumnImage = $settings['two_column_image'] ?? '';
        $this->data = [
            'title' => $page->title,
            'meta_description' => $page->meta_description ?? '',
            'is_published' => $page->is_published,
            'welcome_section_title' => $settings['welcome_section_title'] ?? $defaults['welcome_section_title'] ?? '',
            'welcome_section_body' => $settings['welcome_section_body'] ?? $defaults['welcome_section_body'] ?? '',
            'stats_section_title' => $settings['stats_section_title'] ?? $defaults['stats_section_title'] ?? '',
            'certifications_section_title' => $settings['certifications_section_title'] ?? $defaults['certifications_section_title'] ?? '',
            'employers_section_title' => $settings['employers_section_title'] ?? $defaults['employers_section_title'] ?? '',
            'testimonial_section_title' => $settings['testimonial_section_title'] ?? $defaults['testimonial_section_title'] ?? '',
            'testimonial_section_subtitle' => $settings['testimonial_section_subtitle'] ?? $defaults['testimonial_section_subtitle'] ?? '',
            'two_column_image' => is_string($twoColumnImage) && $twoColumnImage !== '' ? [$twoColumnImage] : [],
            'two_column_image_alt' => $settings['two_column_image_alt'] ?? $defaults['two_column_image_alt'] ?? '',
            'two_column_title' => $settings['two_column_title'] ?? $defaults['two_column_title'] ?? '',
            'two_column_body' => $settings['two_column_body'] ?? $defaults['two_column_body'] ?? '',
            'two_column_image_first' => (bool) (($settings['two_column_image_first'] ?? $defaults['two_column_image_first'] ?? '1') === '1'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Home page')
                    ->description('Title, meta description and publish state for the homepage.')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('meta_description')
                            ->label('Meta description')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Toggle::make('is_published')
                            ->label('Published'),
                    ])
                    ->columns(1),

                Section::make('Welcome section')
                    ->description('Shown below the hero. When set, the title overrides the home page title above.')
                    ->schema([
                        TextInput::make('welcome_section_title')
                            ->label('Title')
                            ->maxLength(255),
                        Textarea::make('welcome_section_body')
                            ->label('Body')
                            ->rows(4)
                            ->maxLength(2000),
                    ])
                    ->columns(1),

                Section::make('Two-column section')
                    ->description('Optional block with image and content. Leave empty to hide on the homepage.')
                    ->schema([
                        FileUpload::make('two_column_image')
                            ->label('Image')
                            ->directory('home')
                            ->disk('public')
                            ->image()
                            ->maxSize(2048)
                            ->nullable(),
                        TextInput::make('two_column_image_alt')
                            ->label('Image alt text')
                            ->maxLength(255),
                        TextInput::make('two_column_title')
                            ->label('Title')
                            ->maxLength(255),
                        Textarea::make('two_column_body')
                            ->label('Content')
                            ->rows(5)
                            ->columnSpanFull(),
                        Toggle::make('two_column_image_first')
                            ->label('Image on left')
                            ->default(true),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Section titles')
                    ->description('Headings for blocks on the homepage.')
                    ->schema([
                        TextInput::make('stats_section_title')
                            ->label('Stats / achievements')
                            ->maxLength(255),
                        TextInput::make('certifications_section_title')
                            ->label('Certifications')
                            ->maxLength(255),
                        TextInput::make('employers_section_title')
                            ->label('Employers / recruiters')
                            ->maxLength(255),
                        TextInput::make('testimonial_section_title')
                            ->label('Testimonials title')
                            ->maxLength(255),
                        Textarea::make('testimonial_section_subtitle')
                            ->label('Testimonials subtitle')
                            ->rows(2)
                            ->maxLength(500),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
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

        $page = PageModel::where('slug', 'home')->firstOrFail();
        $page->update([
            'title' => $data['title'],
            'meta_description' => $data['meta_description'] ?? '',
            'is_published' => (bool) ($data['is_published'] ?? true),
        ]);

        $oldTwoColumnImage = SiteSetting::get('two_column_image');
        $twoColumnImageValue = $data['two_column_image'] ?? [];
        $newTwoColumnImagePath = is_array($twoColumnImageValue) ? ($twoColumnImageValue[0] ?? '') : (string) $twoColumnImageValue;
        if ($oldTwoColumnImage && $newTwoColumnImagePath !== (string) $oldTwoColumnImage) {
            Storage::disk('public')->delete($oldTwoColumnImage);
        }

        $settingKeys = [
            'welcome_section_title',
            'welcome_section_body',
            'stats_section_title',
            'certifications_section_title',
            'employers_section_title',
            'testimonial_section_title',
            'testimonial_section_subtitle',
            'two_column_image',
            'two_column_image_alt',
            'two_column_title',
            'two_column_body',
            'two_column_image_first',
        ];
        foreach ($settingKeys as $key) {
            $value = $data[$key] ?? '';
            if ($key === 'two_column_image') {
                $value = $newTwoColumnImagePath;
            }
            if ($key === 'two_column_image_first') {
                $value = ($value ? '1' : '0');
            }
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
            );
        }

        Notification::make()
            ->title('Homepage content saved.')
            ->success()
            ->send();
    }
}
