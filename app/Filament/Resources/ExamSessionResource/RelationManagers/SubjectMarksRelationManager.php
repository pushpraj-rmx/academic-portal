<?php

namespace App\Filament\Resources\ExamSessionResource\RelationManagers;

use App\Models\Subject;
use App\Models\SubjectMark;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

class SubjectMarksRelationManager extends RelationManager
{
    protected static string $relationship = 'subjectMarks';

    public function form(Form $form): Form
    {
        $owner = $this->getOwnerRecord();
        $sessionId = (int) $owner->id;
        $sessionSubjectIds = $owner->examSessionSubjects()->pluck('subject_id');

        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'enrollment_id', function ($query) use ($owner) {
                        $courseIds = $owner->examSessionSubjects()->with('subject:id,course_id')->get()->pluck('subject.course_id')->unique()->filter()->values()->all();

                        return $query->whereIn('course_id', $courseIds);
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name.' ('.$record->enrollment_id.(filled($record->roll_number) ? ' / '.$record->roll_number : '').')')
                    ->required()
                    ->searchable(['roll_number', 'enrollment_id'])
                    ->preload()
                    ->live(),
                Forms\Components\Select::make('subject_id')
                    ->options(function (Forms\Get $get) use ($sessionSubjectIds, $sessionId): array {
                        $studentId = $get('student_id');
                        if (! $studentId) {
                            return Subject::whereIn('id', $sessionSubjectIds)->pluck('name', 'id')->all();
                        }
                        $student = \App\Models\Student::find($studentId);
                        if (! $student) {
                            return [];
                        }

                        $currentSubjectId = $get('subject_id');
                        $excludedSubjectIds = SubjectMark::subjectIdsForStudentInSession($sessionId, (int) $studentId);
                        if (filled($currentSubjectId)) {
                            $excludedSubjectIds = array_values(array_diff(
                                $excludedSubjectIds,
                                [(int) $currentSubjectId]
                            ));
                        }

                        return Subject::whereIn('id', $sessionSubjectIds)
                            ->where('course_id', $student->course_id)
                            ->when(! empty($excludedSubjectIds), fn ($query) => $query->whereNotIn('id', $excludedSubjectIds))
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->required()
                    ->searchable()
                    // Prevent marks from being reassigned to a different subject on edit.
                    // Each (exam_session_id, student_id, subject_id) row is unique, so if subject_id
                    // changes during editing it can look like one subject replaced another.
                    ->disabledOn('edit'),
                Forms\Components\TextInput::make('marks_obtained')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(9999)
                    ->nullable()
                    ->required(fn (Forms\Get $get) => ! $get('is_absent'))
                    ->hidden(fn (Forms\Get $get) => (bool) $get('is_absent')),
                Forms\Components\Toggle::make('is_absent')
                    ->default(false)
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set, $state) => $set('marks_obtained', $state ? null : 0)),
            ]);
    }

    public function table(Table $table): Table
    {
        $owner = $this->getOwnerRecord();
        $isPublished = $owner->status === 'published';

        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('student.user.name')->label('Student'),
                Tables\Columns\TextColumn::make('subject.name')->label('Subject'),
                Tables\Columns\TextColumn::make('marks_obtained')
                    ->numeric(2)
                    ->placeholder('Absent')
                    ->default('—'),
                Tables\Columns\IconColumn::make('is_absent')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => ! $isPublished)
                    ->mutateFormDataUsing(function (array $data) use ($owner): array {
                        $examSessionId = (int) $owner->id;
                        $studentId = isset($data['student_id']) ? (int) $data['student_id'] : 0;
                        $subjectId = isset($data['subject_id']) ? (int) $data['subject_id'] : 0;

                        if ($studentId > 0 && $subjectId > 0 && SubjectMark::existsFor($examSessionId, $studentId, $subjectId)) {
                            throw ValidationException::withMessages([
                                'subject_id' => 'Marks already exist for this student and subject in this exam session.',
                            ]);
                        }

                        return array_merge($data, ! empty($data['is_absent']) ? ['marks_obtained' => null] : []);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => ! $isPublished)
                    ->mutateFormDataUsing(fn (array $data): array => array_merge($data, ! empty($data['is_absent']) ? ['marks_obtained' => null] : [])),
            ])
            ->bulkActions([]);
    }
}
