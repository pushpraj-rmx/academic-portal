<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->rules(['alpha_dash']),
                Forms\Components\RichEditor::make('body')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('attachment')
                    ->directory('announcements')
                    ->disk('public')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->nullable(),
                Forms\Components\Toggle::make('is_published')
                    ->default(false),
                Forms\Components\DateTimePicker::make('published_at')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('attachment')
                    ->label('PDF')
                    ->boolean()
                    ->getStateUsing(fn (Announcement $record) => filled($record->attachment)),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('announcement.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('announcement.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('announcement.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('announcement.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('announcement.delete') ?? false;
    }
}
