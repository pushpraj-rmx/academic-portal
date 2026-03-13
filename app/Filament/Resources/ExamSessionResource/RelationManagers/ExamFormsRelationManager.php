<?php

namespace App\Filament\Resources\ExamSessionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ExamFormsRelationManager extends RelationManager
{
    protected static string $relationship = 'examForms';

    public function form(Form $form): Form
    {
        $owner = $this->getOwnerRecord();
        $sessionSubjectCourseIds = $owner->examSessionSubjects()->with('subject:id,course_id')->get()->pluck('subject.course_id')->unique()->filter()->values()->all();

        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship(
                        name: 'student',
                        titleAttribute: 'roll_number',
                        modifyQueryUsing: fn ($query) => $query->whereIn('course_id', $sessionSubjectCourseIds)
                    )
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => $record->user->name.' ('.$record->roll_number.')')
                    ->required()
                    ->searchable(['roll_number', 'enrollment_id'])
                    ->preload()
                    ->rules([
                        function () use ($owner): \Closure {
                            return function (string $attribute, $value, \Closure $fail) use ($owner): void {
                                $student = \App\Models\Student::with('course')->find($value);
                                if (! $student) {
                                    return;
                                }
                                $hasSubjectInSession = $owner->examSessionSubjects()
                                    ->whereHas('subject', fn ($q) => $q->where('course_id', $student->course_id))
                                    ->exists();
                                if (! $hasSubjectInSession) {
                                    $fail('This student\'s course has no subjects in this exam session.');
                                }
                            };
                        },
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        $owner = $this->getOwnerRecord();
        $isPublished = $owner->status === 'published';
        $canCreate = ! $isPublished && $owner->status === 'registration_open';

        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('student.user.name')->label('Student'),
                Tables\Columns\TextColumn::make('student.roll_number')->label('Roll No'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'applied' => 'gray',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => $canCreate),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->visible(fn (\App\Models\ExamForm $record): bool => $record->status === 'applied' && ! $isPublished)
                    ->action(function (\App\Models\ExamForm $record): void {
                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);
                        Notification::make()->title('Form approved')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn (\App\Models\ExamForm $record): bool => $record->status === 'applied' && ! $isPublished)
                    ->action(function (\App\Models\ExamForm $record): void {
                        $record->update([
                            'status' => 'rejected',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);
                        Notification::make()->title('Form rejected')->success()->send();
                    }),
                Tables\Actions\EditAction::make()->visible(fn () => ! $isPublished),
                Tables\Actions\DeleteAction::make()->visible(fn () => ! $isPublished),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn () => ! $isPublished),
                ]),
            ]);
    }
}
