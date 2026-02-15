<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $recordTitleAttribute = 'type';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options([
                        'photo' => 'Photo',
                        'id_proof' => 'ID Proof',
                        'marksheet_10' => 'Marksheet (10th)',
                        'marksheet_12' => 'Marksheet (12th)',
                        'other' => 'Other',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('file_path')
                    ->directory('student-documents')
                    ->disk('local')
                    ->required()
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']),
                Forms\Components\TextInput::make('original_name')
                    ->maxLength(255),
                Forms\Components\Toggle::make('verified')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('original_name')->placeholder('—'),
                Tables\Columns\IconColumn::make('verified')->boolean(),
                Tables\Columns\TextColumn::make('verified_at')->dateTime()->placeholder('—'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->url(fn ($record) => route('admin.student-documents.download', $record))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-down-tray'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
