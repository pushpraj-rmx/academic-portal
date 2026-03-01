<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatCounterResource\Pages;
use App\Models\StatCounter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class StatCounterResource extends Resource
{
    protected static ?string $model = StatCounter::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Forms\Components\TextInput::make('suffix')
                    ->maxLength(20),
                Forms\Components\TextInput::make('icon')
                    ->helperText('Optional icon key for custom display.')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                Forms\Components\Toggle::make('is_published')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->sortable(),
                Tables\Columns\TextColumn::make('suffix')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')->label('Published'),
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
            'index' => Pages\ListStatCounters::route('/'),
            'create' => Pages\CreateStatCounter::route('/create'),
            'edit' => Pages\EditStatCounter::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('stat-counter.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('stat-counter.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('stat-counter.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('stat-counter.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('stat-counter.delete') ?? false;
    }
}
