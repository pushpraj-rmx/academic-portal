<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseCategoryResource\Pages;
use App\Models\CourseCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CourseCategoryResource extends Resource
{
    protected static ?string $model = CourseCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Academic';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (?string $state, Set $set) => $set('slug', Str::slug($state ?? ''))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->rules(['alpha_dash']),
                TiptapEditor::make('description')
                    ->profile('default')
                    ->output(TiptapOutput::Html)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image_path')
                    ->label('Category image')
                    ->disk('public')
                    ->directory('course-categories')
                    ->image()
                    ->maxSize(4096)
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                        $name = normalize_upload_filename($file->getClientOriginalName());

                        return str_contains($name, '.') ? $name : $name.'.'.$file->getClientOriginalExtension();
                    })
                    ->helperText('Optional image for the category cards, max 4 MB.'),
                Forms\Components\TextInput::make('image_alt')
                    ->label('Image alt text')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->disk('public')
                    ->label('Image')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('courses_count')
                    ->label('Courses')
                    ->counts('courses')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->sortable()->numeric(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (CourseCategory $record): void {
                        if ($record->courses()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Cannot delete category')
                                ->body('This category has courses. Reassign or delete the courses first.')
                                ->danger()
                                ->send();
                            throw ValidationException::withMessages([
                                'category' => ['This category has courses. Reassign or delete the courses first.'],
                            ]);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records): void {
                            $withCourses = $records->filter(fn (CourseCategory $r) => $r->courses()->exists());
                            if ($withCourses->isNotEmpty()) {
                                $names = $withCourses->pluck('name')->join(', ');
                                \Filament\Notifications\Notification::make()
                                    ->title('Cannot delete some categories')
                                    ->body("These categories have courses: {$names}. Reassign or delete the courses first.")
                                    ->danger()
                                    ->send();
                                throw ValidationException::withMessages([
                                    'category' => ['One or more selected categories have courses. Reassign or delete the courses first.'],
                                ]);
                            }
                        }),
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
            'index' => Pages\ListCourseCategories::route('/'),
            'create' => Pages\CreateCourseCategory::route('/create'),
            'edit' => Pages\EditCourseCategory::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('category.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('category.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('category.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('category.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('category.delete') ?? false;
    }
}
