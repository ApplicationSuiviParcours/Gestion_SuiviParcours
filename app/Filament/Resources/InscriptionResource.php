<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InscriptionResource\Pages;
use App\Models\Inscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Carbon\Carbon;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class InscriptionResource extends Resource
{
    protected static ?string $model = Inscription::class;

    protected static ?string $navigationGroup = 'Scolarité';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Inscriptions';
    protected static ?string $pluralModelLabel = 'Inscriptions';
    protected static ?int $navigationSort = 7;

    // 🔐 SÉCURITÉ
    public static function canViewAny(): bool
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
        return 'Le nombre d\'inscriptions';
    }

    /* =========================
        FORMULAIRE
    ========================== */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Inscription')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Forms\Components\Select::make('eleve_id')
                            ->label('Élève')
                            ->relationship('eleve', 'nom')
                            ->searchable()
                            ->required()
                            ->prefixIcon('heroicon-o-user'),

                        Forms\Components\Select::make('classe_id')
                            ->label('Classe')
                            ->relationship('classe', 'nom_classe')
                            ->required()
                            ->prefixIcon('heroicon-o-rectangle-group'),

                        Forms\Components\Select::make('annee_id')
                            ->label('Année scolaire')
                            ->relationship('annee', 'libelle')
                            ->required()
                            ->prefixIcon('heroicon-o-calendar'),

                        Forms\Components\DatePicker::make('date_inscription')
                            ->label('Date d\'inscription')
                            ->required()
                            ->prefixIcon('heroicon-o-clock'),

                        Forms\Components\TextInput::make('statut')
                            ->label('Statut')
                            ->required()
                            ->prefixIcon('heroicon-o-check-circle'),
                    ])->columns(2),
            ]);
    }

    /* =========================
        TABLE
    ========================== */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('eleve.nom')
                    ->label('Élève')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('classe.nom_classe')
                    ->label('Classe')
                    ->sortable()
                    ->icon('heroicon-o-rectangle-group'),

                Tables\Columns\TextColumn::make('annee.libelle')
                    ->label('Année')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('date_inscription')
                    ->label('Date')
                    ->date()
                    ->sortable()
                    ->icon('heroicon-o-clock'),

                Tables\Columns\TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->icon('heroicon-o-check-circle'),

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
                SelectFilter::make('classe_id')
                    ->label('Classe')
                    ->relationship('classe', 'nom_classe'),

                SelectFilter::make('annee_id')
                    ->label('Année')
                    ->relationship('annee', 'libelle'),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Depuis'),
                        DatePicker::make('created_until')->label('Jusqu\'à'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'] ?? null,
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'] ?? null,
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make(
                                'Depuis ' . Carbon::parse($data['created_from'])->toFormattedDateString()
                            )->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make(
                                'Jusqu\'à ' . Carbon::parse($data['created_until'])->toFormattedDateString()
                            )->removeField('created_until');
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

    /* =========================
        INFOLIST
    ========================== */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Détails de l\'inscription')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        TextEntry::make('eleve.nom')
                            ->label('Élève')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('classe.nom_classe')
                            ->label('Classe')
                            ->icon('heroicon-o-rectangle-group'),

                        TextEntry::make('annee.libelle')
                            ->label('Année scolaire')
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('date_inscription')
                            ->label('Date d\'inscription')
                            ->date()
                            ->icon('heroicon-o-clock'),

                        TextEntry::make('statut')
                            ->label('Statut')
                            ->badge()
                            ->icon('heroicon-o-check-circle'),
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
            'index' => Pages\ListInscriptions::route('/'),
            'create' => Pages\CreateInscription::route('/create'),
            'edit' => Pages\EditInscription::route('/{record}/edit'),
        ];
    }
}
