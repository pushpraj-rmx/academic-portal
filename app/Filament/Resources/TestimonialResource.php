<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('body')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('avatar')
                    ->directory('testimonials')
                    ->disk('public')
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth(200)
                    ->imageResizeTargetHeight(200)
                    ->maxSize(2048)
                    ->nullable()
                    ->helperText('Profile picture for the testimonial. Recommended: square image, max 2 MB.'),
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
                Tables\Columns\ImageColumn::make('avatar')
                    ->disk('public')
                    ->circular()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('body')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')->label('Published'),
            ])
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('testimonial.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('testimonial.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('testimonial.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('testimonial.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('testimonial.delete') ?? false;
    }
}
