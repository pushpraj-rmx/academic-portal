<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
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
                    ->unique(table: User::class, column: 'email'),
                Forms\Components\TextInput::make('password')
                    ->label('Initial password')
                    ->password()
                    ->required()
                    ->minLength(8),
                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('enrollment_id')
                    ->label('Enrollment ID')
                    ->maxLength(255)
                    ->unique(table: Student::class, column: 'enrollment_id'),
                Forms\Components\TextInput::make('roll_number')
                    ->required()
                    ->maxLength(255)
                    ->rules([
                        fn (callable $get) => Rule::unique('students', 'roll_number')
                            ->where('course_id', $get('course_id')),
                    ]),
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
