<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpecializationResource\Pages;
use App\Models\Specialization;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SpecializationResource extends Resource
{
    protected static ?string $model = Specialization::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Academic';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (?string $state, Set $set) => $set('slug', Str::slug($state ?? ''))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->rules(['alpha_dash'])
                    ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule, $get) {
                        return $rule->where('course_id', $get('course_id'));
                    }),
                Forms\Components\Textarea::make('description')->maxLength(65535)->columnSpanFull(),
                Forms\Components\Textarea::make('industry_relevance')->maxLength(65535)->columnSpanFull(),
                Forms\Components\Textarea::make('career_outcomes')->maxLength(65535)->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')->default(true),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('course.name')->label('Course')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->sortable()->numeric(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Specialization $record): void {
                        if ($record->syllabi()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Cannot delete specialization')
                                ->body('This specialization has syllabi linked. Remove or reassign the syllabi first.')
                                ->danger()
                                ->send();
                            throw ValidationException::withMessages([
                                'specialization' => ['This specialization has syllabi linked. Remove or reassign the syllabi first.'],
                            ]);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records): void {
                            $withSyllabi = $records->filter(fn (Specialization $r) => $r->syllabi()->exists());
                            if ($withSyllabi->isNotEmpty()) {
                                $names = $withSyllabi->pluck('name')->join(', ');
                                \Filament\Notifications\Notification::make()
                                    ->title('Cannot delete some specializations')
                                    ->body("These specializations have syllabi linked: {$names}. Remove or reassign the syllabi first.")
                                    ->danger()
                                    ->send();
                                throw ValidationException::withMessages([
                                    'specialization' => ['One or more selected specializations have syllabi linked. Remove or reassign the syllabi first.'],
                                ]);
                            }
                        }),
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
            'index' => Pages\ListSpecializations::route('/'),
            'create' => Pages\CreateSpecialization::route('/create'),
            'edit' => Pages\EditSpecialization::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('specialization.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('specialization.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('specialization.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('specialization.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('specialization.delete') ?? false;
    }
}
