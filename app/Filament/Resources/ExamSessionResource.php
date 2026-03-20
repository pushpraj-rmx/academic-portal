<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamSessionResource\Pages;
use App\Filament\Resources\ExamSessionResource\RelationManagers;
use App\Models\ExamSession;
use App\Services\ExamResultService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExamSessionResource extends Resource
{
    protected static ?string $model = ExamSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Examination';

    // Ensure "Exam sessions" appears before import pages in the Examination menu.
    protected static ?int $navigationSort = 0;

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
                Forms\Components\TextInput::make('academic_year')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. 2025-2026'),
                Forms\Components\Select::make('session_type')
                    ->options([
                        'regular' => 'Regular',
                        'supplementary' => 'Supplementary',
                        'improvement' => 'Improvement',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('start_date'),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'registration_open' => 'Registration Open',
                        'completed' => 'Completed',
                        'published' => 'Published',
                    ])
                    ->default('draft')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('academic_year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('session_type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'registration_open' => 'info',
                        'completed' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'registration_open' => 'Registration Open',
                        'completed' => 'Completed',
                        'published' => 'Published',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('previewResults')
                    ->label('Preview Results')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (ExamSession $record): bool => in_array($record->status, ['completed', 'published'], true))
                    ->action(function (ExamSession $record): void {
                        $service = app(ExamResultService::class);
                        $results = $service->computeResultsForSession($record);
                        $message = count($results).' result(s) computed.';
                        Notification::make()
                            ->title('Results Preview')
                            ->body($message)
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn (ExamSession $record): bool => $record->status === 'completed')
                    ->requiresConfirmation()
                    ->action(function (ExamSession $record): void {
                        $record->update([
                            'status' => 'published',
                            'published_at' => now(),
                        ]);
                        Notification::make()
                            ->title('Session published')
                            ->success()
                            ->send();
                    }),
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
        return [
            RelationManagers\ExamSessionSubjectsRelationManager::class,
            RelationManagers\ExamFormsRelationManager::class,
            RelationManagers\SubjectMarksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExamSessions::route('/'),
            'create' => Pages\CreateExamSession::route('/create'),
            'edit' => Pages\EditExamSession::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('exam-session.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('exam-session.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('exam-session.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('exam-session.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('exam-session.delete') ?? false;
    }
}
