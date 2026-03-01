<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NavItemResource\Pages;
use App\Models\NavItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class NavItemResource extends Resource
{
    protected static ?string $model = NavItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-left';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->label('Parent')
                    ->options(function (?Model $record) {
                        $query = NavItem::roots()->ordered();
                        if ($record) {
                            $query->where('id', '!=', $record->getKey());
                        }

                        return $query->pluck('label', 'id');
                    })
                    ->nullable()
                    ->searchable(),
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('route_name')
                    ->label('Route')
                    ->options(self::routeNameOptions())
                    ->nullable()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('url', null)),
                Forms\Components\KeyValue::make('route_params')
                    ->label('Route parameters (e.g. page => about for pages.show)')
                    ->keyLabel('Parameter')
                    ->valueLabel('Value')
                    ->nullable()
                    ->visible(fn (Forms\Get $get) => (bool) $get('route_name')),
                Forms\Components\TextInput::make('url')
                    ->label('Custom URL')
                    ->url()
                    ->maxLength(500)
                    ->nullable()
                    ->visible(fn (Forms\Get $get) => ! $get('route_name')),
                Forms\Components\Select::make('show_when')
                    ->label('Show when')
                    ->options([
                        null => 'Always',
                        'guest' => 'Guest only',
                        'auth' => 'Authenticated only',
                        'auth_admin' => 'Authenticated (non-student) only',
                    ])
                    ->nullable(),
                Forms\Components\Select::make('dynamic_source')
                    ->label('Dynamic children')
                    ->options([
                        null => 'None',
                        'course_categories' => 'Course categories',
                    ])
                    ->nullable()
                    ->visible(fn (Forms\Get $get) => ! $get('parent_id')),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                Forms\Components\Toggle::make('is_visible')
                    ->default(true),
            ]);
    }

    /**
     * @return array<string, string>
     */
    protected static function routeNameOptions(): array
    {
        return [
            'home' => 'home',
            'pages.show' => 'pages.show (CMS page)',
            'academic.index' => 'academic.index (Courses)',
            'academic.category.show' => 'academic.category.show (by slug)',
            'notices.index' => 'notices.index',
            'examination.forms' => 'examination.forms',
            'examination.center' => 'examination.center',
            'examination.faqs' => 'examination.faqs',
            'examination.grading-system' => 'examination.grading-system',
            'results.index' => 'results.index',
            'students.application-forms' => 'students.application-forms',
            'students.verification' => 'students.verification',
            'students.pay-fee' => 'students.pay-fee',
            'placements.recruiters' => 'placements.recruiters',
            'contact.index' => 'contact.index',
            'login' => 'login',
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->label('#'),
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.label')
                    ->label('Parent')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('route_name')
                    ->placeholder('Custom URL')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_visible')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('show_when')
                    ->placeholder('Always')
                    ->toggleable(),
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
            'index' => Pages\ListNavItems::route('/'),
            'create' => Pages\CreateNavItem::route('/create'),
            'edit' => Pages\EditNavItem::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('nav-item.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('nav-item.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('nav-item.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('nav-item.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('nav-item.delete') ?? false;
    }
}
