<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationResource\Pages;
use App\Models\Evaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Carbon\Carbon;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static ?string $navigationGroup = 'Scolarité';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Évaluations';
    protected static ?string $pluralModelLabel = 'Évaluations';
    protected static ?int $navigationSort = 9;
    protected static ?string $recordTitleAttribute = 'type_evaluation';

    // 🔐 Sécurité
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Enseignant', 'Scolarite']);
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
        return 'Le nombre de types d\'évaluation';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations sur l\'évaluation')
                    ->schema([
                        Forms\Components\Select::make('type_evaluation')
                            ->label('Type d\'évaluation')
                            ->options([
                                'devoir' => 'Devoir',
                                'examen' => 'Examen',
                            ])
                            ->required()
                            ->prefixIcon('heroicon-o-document-text'),

                        Forms\Components\DatePicker::make('date_evaluation')
                            ->label('Date d\'évaluation')
                            ->required()
                            ->prefixIcon('heroicon-o-calendar'),

                        Forms\Components\Select::make('classe_id')
                            ->relationship('classe','nom_classe')
                            ->label('Classe')
                            ->required()
                            ->prefixIcon('heroicon-o-rectangle-stack'),

                        Forms\Components\Select::make('matiere_id')
                            ->relationship('matiere','libelle')
                            ->label('Matière')
                            ->required()
                            ->prefixIcon('heroicon-o-book-open'),

                        Forms\Components\Select::make('annee_id')
                            ->relationship('annee','libelle')
                            ->label('Année scolaire')
                            ->required()
                            ->prefixIcon('heroicon-o-calendar-days'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type_evaluation')
                    ->label('Type')
                    ->icon('heroicon-o-document-text')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date_evaluation')
                    ->label('Date')
                    ->icon('heroicon-o-calendar')
                    ->date()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('classe.nom_classe')
                    ->label('Classe')
                    ->icon('heroicon-o-rectangle-stack')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('matiere.libelle')
                    ->label('Matière')
                    ->icon('heroicon-o-book-open')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('annee.libelle')
                    ->label('Année')
                    ->icon('heroicon-o-calendar-days')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->icon('heroicon-o-clock')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Mis à jour le')
                    ->icon('heroicon-o-clock')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type_evaluation')
                    ->label('Type d\'évaluation')
                    ->options([
                        'devoir' => 'Devoir',
                        'interrogation' => 'Interrogation',
                        'examen' => 'Examen',
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
                Section::make('Détails de l\'évaluation')
                    ->schema([
                        TextEntry::make('type_evaluation')
                            ->label('Type d\'évaluation')
                            ->icon('heroicon-o-document-text'),

                        TextEntry::make('date_evaluation')
                            ->label('Date')
                            ->icon('heroicon-o-calendar')
                            ->date(),

                        TextEntry::make('classe.nom_classe')
                            ->label('Classe')
                            ->icon('heroicon-o-rectangle-stack'),

                        TextEntry::make('matiere.libelle')
                            ->label('Matière')
                            ->icon('heroicon-o-book-open'),

                        TextEntry::make('annee.libelle')
                            ->label('Année scolaire')
                            ->icon('heroicon-o-calendar-days'),
                    ])->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluation::route('/create'),
            'edit' => Pages\EditEvaluation::route('/{record}/edit'),
        ];
    }
}
