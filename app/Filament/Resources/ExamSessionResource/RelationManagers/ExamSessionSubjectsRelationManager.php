<?php

namespace App\Filament\Resources\ExamSessionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ExamSessionSubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'examSessionSubjects';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('exam_date'),
                Forms\Components\TimePicker::make('exam_time'),
            ]);
    }

    public function table(Table $table): Table
    {
        $owner = $this->getOwnerRecord();
        $isPublished = $owner->status === 'published';

        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')->label('Subject'),
                Tables\Columns\TextColumn::make('subject.code')->label('Code'),
                Tables\Columns\TextColumn::make('exam_date')->date()->placeholder('—'),
                Tables\Columns\TextColumn::make('exam_time')->placeholder('—'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => ! $isPublished),
            ])
            ->actions([
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
