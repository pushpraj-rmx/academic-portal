<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SyllabusResource\Pages;
use App\Models\Syllabus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SyllabusResource extends Resource
{
    protected static ?string $model = Syllabus::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $navigationGroup = 'Academic';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),
                Forms\Components\Select::make('specialization_id')
                    ->relationship('specialization', 'name', fn ($query, $get) => $query->where('course_id', $get('course_id')))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\TextInput::make('academic_year')->maxLength(255)->nullable(),
                Forms\Components\TextInput::make('version')->maxLength(255)->nullable(),
                Forms\Components\FileUpload::make('file_path')
                    ->directory('syllabus')
                    ->disk('public')
                    ->acceptedFileTypes(['application/pdf'])
                    ->required(),
                Forms\Components\Toggle::make('is_active')->default(true),
                Forms\Components\DateTimePicker::make('published_at')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('course.name')->label('Course')->sortable(),
                Tables\Columns\TextColumn::make('specialization.name')->label('Specialization')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('academic_year')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('version')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
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
            'index' => Pages\ListSyllabi::route('/'),
            'create' => Pages\CreateSyllabus::route('/create'),
            'edit' => Pages\EditSyllabus::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('syllabus.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('syllabus.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('syllabus.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('syllabus.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('syllabus.delete') ?? false;
    }
}
