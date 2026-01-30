<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClasseResource\Pages;
use App\Models\Classe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Carbon\Carbon;

class ClasseResource extends Resource
{
    protected static ?string $model = Classe::class;
    protected static ?string $navigationGroup = 'Scolarité';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?int $navigationSort = 3;
    protected static ?string $pluralModelLabel = 'Classes';
    protected static ?string $navigationLabel = 'Classes';
    protected static ?string $recordTitleAttribute = 'nom_classe';

    // 🔐 SÉCURITÉ
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Scolarite']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Scolarite']);
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Scolarite']);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Scolarite']);
    }


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Le nombre de classes';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations sur la classe')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([
                        Forms\Components\TextInput::make('nom_classe')
                            ->label('Nom de la classe')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan('full')
                            ->prefixIcon('heroicon-o-user'),

                        Forms\Components\Select::make('niveau')
                            ->label('Niveau')
                            ->options([
                                'prescolaire' => 'Préscolaire',
                                'primaire' => 'Primaire',
                                'college' => 'Collège',
                                'lycee' => 'Lycée',
                            ])
                            ->required()
                            ->prefixIcon('heroicon-o-academic-cap'),

                        Forms\Components\TextInput::make('filiere')
                            ->label('Filière')
                            ->visible(fn ($get) => $get('niveau') === 'lycee')
                            ->placeholder('Exemple : Science, Littéraire')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-book-open'),

                        Forms\Components\TextInput::make('effectif_max')
                            ->label('Effectif maximum')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(200)
                            ->suffix('élèves')
                            ->prefixIcon('heroicon-o-users'),
                    ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom_classe')
                    ->label('Classe')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('niveau')
                    ->label('Niveau')
                    ->badge()
                    ->searchable()
                    ->icon('heroicon-o-academic-cap'),

                Tables\Columns\TextColumn::make('filiere')
                    ->label('Filière')
                    ->searchable()
                    ->icon('heroicon-o-book-open'),

                Tables\Columns\TextColumn::make('effectif_max')
                    ->label('Effectif')
                    ->counts('eleves')
                    ->badge()
                    ->icon('heroicon-o-users'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Mis à jour le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                SelectFilter::make('niveau')
                    ->label('Niveau')
                    ->options([
                        'prescolaire' => 'Préscolaire',
                        'primaire' => 'Primaire',
                        'college' => 'Collège',
                        'lycee' => 'Lycée',
                    ]),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Depuis'),
                        DatePicker::make('created_until')->label('Jusqu\'à'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Depuis ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('Jusqu\'à ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informations sur la classe')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([
                        TextEntry::make('nom_classe')
                            ->label('Nom de la classe')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('niveau')
                            ->label('Niveau')
                            ->icon('heroicon-o-academic-cap'),

                        TextEntry::make('filiere')
                            ->label('Filière')
                            ->icon('heroicon-o-book-open'),

                        TextEntry::make('effectif_max')
                            ->label('Effectif maximum')
                            ->icon('heroicon-o-users'),
                    ])->columns(4),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClasses::route('/'),
            'create' => Pages\CreateClasse::route('/create'),
            'edit' => Pages\EditClasse::route('/{record}/edit'),
        ];
    }
}
