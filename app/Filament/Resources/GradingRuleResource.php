<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradingRuleResource\Pages;
use App\Models\GradingRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Model;

class GradingRuleResource extends Resource
{
    protected static ?string $model = GradingRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Examination';

    // Place grading rules after exam session and marks import.
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('grade')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('min_percentage')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01),
                Forms\Components\TextInput::make('max_percentage')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01),
                Forms\Components\TextInput::make('gpa')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(10)
                    ->step(0.01),
                TiptapEditor::make('description')
                    ->label('Description')
                    ->profile('default')
                    ->output(TiptapOutput::Html)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('grade')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_percentage')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_percentage')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('gpa')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
            'index' => Pages\ListGradingRules::route('/'),
            'create' => Pages\CreateGradingRule::route('/create'),
            'edit' => Pages\EditGradingRule::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('grading-rule.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('grading-rule.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('grading-rule.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('grading-rule.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('grading-rule.delete') ?? false;
    }
}
