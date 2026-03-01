<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificationResource\Pages;
use App\Models\Certification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CertificationResource extends Resource
{
    protected static ?string $model = Certification::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->disk('public')
                    ->directory('certifications')
                    ->image()
                    ->maxSize(2048)
                    ->required(),
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
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->label('Logo'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListCertifications::route('/'),
            'create' => Pages\CreateCertification::route('/create'),
            'edit' => Pages\EditCertification::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('certification.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('certification.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('certification.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('certification.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('certification.delete') ?? false;
    }
}
