<?php

namespace App\Filament\Resources\ExamSessionResource\RelationManagers;

use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SubjectMarksRelationManager extends RelationManager
{
    protected static string $relationship = 'subjectMarks';

    public function form(Form $form): Form
    {
        $owner = $this->getOwnerRecord();
        $sessionSubjectIds = $owner->examSessionSubjects()->pluck('subject_id');

        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'roll_number', function ($query) use ($owner) {
                        $courseIds = $owner->examSessionSubjects()->with('subject:id,course_id')->get()->pluck('subject.course_id')->unique()->filter()->values()->all();

                        return $query->whereIn('course_id', $courseIds);
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name.' ('.$record->roll_number.')')
                    ->required()
                    ->searchable(['roll_number'])
                    ->preload()
                    ->live(),
                Forms\Components\Select::make('subject_id')
                    ->options(function (Forms\Get $get) use ($sessionSubjectIds): array {
                        $studentId = $get('student_id');
                        if (! $studentId) {
                            return Subject::whereIn('id', $sessionSubjectIds)->pluck('name', 'id')->all();
                        }
                        $student = \App\Models\Student::find($studentId);
                        if (! $student) {
                            return [];
                        }

                        return Subject::whereIn('id', $sessionSubjectIds)
                            ->where('course_id', $student->course_id)
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->required()
                    ->searchable(),
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
                    ->mutateFormDataUsing(fn (array $data): array => array_merge($data, ! empty($data['is_absent']) ? ['marks_obtained' => null] : [])),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => ! $isPublished)
                    ->mutateFormDataUsing(fn (array $data): array => array_merge($data, ! empty($data['is_absent']) ? ['marks_obtained' => null] : [])),
            ])
            ->bulkActions([]);
    }
}
