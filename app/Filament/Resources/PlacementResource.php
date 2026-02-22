<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlacementResource\Pages;
use App\Models\Course;
use App\Models\Placement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PlacementResource extends Resource
{
    protected static ?string $model = Placement::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Placement';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship(
                        'student',
                        'enrollment_id',
                        fn ($query) => $query->orderBy('enrollment_id')
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->enrollment_id.' — '.$record->user?->name)
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('recruiter_id')
                    ->relationship('recruiter', 'name', fn ($query) => $query->orderBy('name'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('academic_year')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. 2025-2026')
                    ->rules([
                        function (Get $get, $livewire): \Closure {
                            return function (string $attribute, mixed $value, \Closure $fail): void {
                                $query = Placement::query()
                                    ->where('student_id', $get('student_id'))
                                    ->where('recruiter_id', $get('recruiter_id'))
                                    ->where('academic_year', $value);
                                if (isset($livewire->record)) {
                                    $query->whereKeyNot($livewire->record->getKey());
                                }
                                if ($query->exists()) {
                                    $fail(__('A placement for this student, recruiter and academic year already exists.'));
                                }
                            };
                        },
                    ]),
                Forms\Components\Select::make('placement_type')
                    ->options([
                        'job' => 'Job',
                        'internship' => 'Internship',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('designation')
                    ->maxLength(255),
                Forms\Components\TextInput::make('package_amount')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01),
                Forms\Components\Select::make('status')
                    ->options([
                        'offered' => 'Offered',
                        'joined' => 'Joined',
                        'declined' => 'Declined',
                    ])
                    ->default('offered')
                    ->required(),
                Forms\Components\DatePicker::make('offer_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.enrollment_id')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('recruiter.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('academic_year')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('placement_type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('designation')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('package_amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'offered' => 'warning',
                        'joined' => 'success',
                        'declined' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('offer_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year')
                    ->options(fn () => Placement::query()->distinct()->pluck('academic_year', 'academic_year')->toArray()),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'offered' => 'Offered',
                        'joined' => 'Joined',
                        'declined' => 'Declined',
                    ]),
                Tables\Filters\SelectFilter::make('recruiter_id')
                    ->relationship('recruiter', 'name')
                    ->label('Recruiter')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('Course')
                    ->options(fn () => Course::query()->pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data): void {
                        if (! empty($data['value'])) {
                            $query->whereHas('student', fn (Builder $q): Builder => $q->where('course_id', $data['value']));
                        }
                    }),
            ])
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
            'index' => Pages\ListPlacements::route('/'),
            'create' => Pages\CreatePlacement::route('/create'),
            'edit' => Pages\EditPlacement::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('placement.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('placement.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('placement.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('placement.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('placement.delete') ?? false;
    }
}
