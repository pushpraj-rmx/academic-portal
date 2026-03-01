<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DownloadableFormResource\Pages;
use App\Models\DownloadableForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DownloadableFormResource extends Resource
{
    protected static ?string $model = DownloadableForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('file_path')
                    ->label('File')
                    ->disk('public')
                    ->directory('downloadable-forms')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->required(),
                Forms\Components\Select::make('category')
                    ->required()
                    ->default('general')
                    ->options([
                        'exam' => 'Exam',
                        'admission' => 'Admission',
                        'general' => 'General',
                    ]),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'exam' => 'Exam',
                        'admission' => 'Admission',
                        'general' => 'General',
                    ]),
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
            'index' => Pages\ListDownloadableForms::route('/'),
            'create' => Pages\CreateDownloadableForm::route('/create'),
            'edit' => Pages\EditDownloadableForm::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('downloadable-form.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('downloadable-form.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('downloadable-form.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('downloadable-form.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('downloadable-form.delete') ?? false;
    }
}
