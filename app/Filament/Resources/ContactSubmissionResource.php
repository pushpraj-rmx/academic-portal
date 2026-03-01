<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactSubmissionResource\Pages;
use App\Models\ContactSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContactSubmissionResource extends Resource
{
    protected static ?string $model = ContactSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->disabled(),
                Forms\Components\TextInput::make('email')
                    ->disabled(),
                Forms\Components\TextInput::make('phone')
                    ->disabled(),
                Forms\Components\TextInput::make('subject')
                    ->disabled(),
                Forms\Components\Textarea::make('message')
                    ->rows(8)
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_read'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\IconColumn::make('is_read')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')->label('Read'),
            ])
            ->actions([
                Tables\Actions\Action::make('markRead')
                    ->label('Mark as read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (ContactSubmission $record): bool => ! $record->is_read)
                    ->action(function (ContactSubmission $record): void {
                        $record->update(['is_read' => true]);
                        Notification::make()->title('Marked as read')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markRead')
                        ->label('Mark selected as read')
                        ->icon('heroicon-o-check')
                        ->action(function ($records): void {
                            $records->each->update(['is_read' => true]);
                        }),
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
            'index' => Pages\ListContactSubmissions::route('/'),
            'edit' => Pages\EditContactSubmission::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('contact-submission.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('contact-submission.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('contact-submission.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('contact-submission.delete') ?? false;
    }
}
