<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroSlideResource\Pages;
use App\Models\HeroSlide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('subtitle')
                    ->maxLength(255),
                Forms\Components\TextInput::make('button_text')
                    ->maxLength(100),
                Forms\Components\TextInput::make('button_link')
                    ->url()
                    ->maxLength(500),
                Forms\Components\FileUpload::make('image')
                    ->disk('public')
                    ->directory('hero-slides')
                    ->image()
                    ->maxSize(4096)
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                        $name = normalize_upload_filename($file->getClientOriginalName());

                        return str_contains($name, '.') ? $name : $name.'.'.$file->getClientOriginalExtension();
                    })
                    ->helperText('Recommended landscape image for hero slider, max 4 MB.'),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                Forms\Components\Toggle::make('is_published')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->label('Image')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtitle')
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('button_text')
                    ->label('Button')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')->label('Published'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHeroSlides::route('/'),
            'create' => Pages\CreateHeroSlide::route('/create'),
            'edit' => Pages\EditHeroSlide::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hero-slide.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hero-slide.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('hero-slide.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('hero-slide.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('hero-slide.delete') ?? false;
    }
}
