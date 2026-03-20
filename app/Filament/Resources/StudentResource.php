<?php

namespace App\Filament\Resources;

use App\Filament\Pages\ImportStudents;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Student';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Student name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->rules([
                        fn (callable $get) => (function () use ($get) {
                            $rule = Rule::unique('users', 'email');

                            // On edit, Livewire requests don't have route params. We can safely
                            // ignore the current student's related user_id from form state.
                            $userId = $get('user_id');
                            if (filled($userId)) {
                                $rule->ignore($userId);
                            }

                            return $rule;
                        })(),
                    ]),
                Forms\Components\TextInput::make('password')
                    ->label('Initial password')
                    ->password()
                    ->visibleOn('create')
                    ->required()
                    ->minLength(8)
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? $state : null),
                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('enrollment_id')
                    ->label('Enrollment ID')
                    ->maxLength(255)
                    ->unique(table: Student::class, column: 'enrollment_id', ignoreRecord: true),
                Forms\Components\TextInput::make('roll_number')
                    ->nullable()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule, $get) {
                        return $rule->where('course_id', $get('course_id'));
                    })
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? $state : null),
                Forms\Components\TextInput::make('father_name')
                    ->label('Father name')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('mother_name')
                    ->label('Mother name')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date_of_birth')
                    ->label('Date of birth')
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->maxDate(now()),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(50),
                Forms\Components\TextInput::make('alternate_phone')
                    ->tel()
                    ->maxLength(50),
                Forms\Components\Select::make('verification_status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrollment_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roll_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Course')
                    ->sortable(),
                Tables\Columns\TextColumn::make('verification_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->relationship('course', 'name')
                    ->label('Course'),
                Tables\Filters\SelectFilter::make('verification_status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('importStudents')
                    ->label('Import students')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('secondary')
                    ->url(ImportStudents::getUrl())
                    ->visible(fn () => auth()->user()?->can('student.create') ?? false),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Student $record) => $record->update([
                        'verification_status' => 'verified',
                        'verified_at' => now(),
                        'verified_by' => auth()->id(),
                    ]))
                    ->visible(fn (Student $record) => $record->verification_status === 'pending')
                    ->authorize('verify'),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Student $record) => $record->update([
                        'verification_status' => 'rejected',
                        'verified_at' => null,
                        'verified_by' => null,
                    ]))
                    ->visible(fn (Student $record) => $record->verification_status === 'pending')
                    ->authorize('verify'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Student $record): void {
                        try {
                            $record->delete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->title('Cannot delete student')
                                ->body('This student is linked to placement/results/exam records. Remove dependent records first.')
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records): void {
                            $deleted = 0;
                            $blocked = 0;

                            foreach ($records as $record) {
                                try {
                                    $record->delete();
                                    $deleted++;
                                } catch (QueryException $e) {
                                    $blocked++;
                                }
                            }

                            if ($deleted > 0) {
                                Notification::make()
                                    ->title("Deleted {$deleted} student(s).")
                                    ->success()
                                    ->send();
                            }

                            if ($blocked > 0) {
                                Notification::make()
                                    ->title("Skipped {$blocked} student(s).")
                                    ->body('Some students are linked to placement/results/exam records and cannot be deleted yet.')
                                    ->warning()
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('student.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('student.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('student.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('student.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('student.delete') ?? false;
    }
}
