<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmploiDuTempsResource\Pages;
use App\Models\EmploiDuTemps;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class EmploiDuTempsResource extends Resource
{
    protected static ?string $model = EmploiDuTemps::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Emplois du Temps';
    protected static ?string $pluralModelLabel = 'Emplois du Temps';
    protected static ?string $navigationGroup = 'Scolarité';
    protected static ?int $navigationSort = 11;

    protected static ?string $recordTitleAttribute = 'id';

    // 🔐 GESTION DES RÔLES
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Scolarite', 'Enseignant']);
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
        return static::getModel()::count() > 10 ? 'warning' : 'success';
    }

    // 📝 FORMULAIRE
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails de l’emploi du temps')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\Select::make('classe_id')
                            ->relationship('classe', 'nom_classe')
                            ->label('Classe')
                            ->required()
                            ->searchable()
                            ->prefixIcon('heroicon-o-academic-cap'),

                        Forms\Components\Select::make('matiere_id')
                            ->relationship('matiere', 'libelle')
                            ->label('Matière')
                            ->required()
                            ->searchable()
                            ->prefixIcon('heroicon-o-book-open'),

                        Forms\Components\Select::make('enseignant_id')
                            ->relationship('enseignant', 'name')
                            ->label('Enseignant')
                            ->required()
                            ->searchable()
                            ->prefixIcon('heroicon-o-user'),

                        Forms\Components\Select::make('jour')
                            ->label('Jour')
                            ->options([
                                'Lundi' => 'Lundi',
                                'Mardi' => 'Mardi',
                                'Mercredi' => 'Mercredi',
                                'Jeudi' => 'Jeudi',
                                'Vendredi' => 'Vendredi',
                                'Samedi' => 'Samedi',
                            ])
                            ->required()
                            ->prefixIcon('heroicon-o-calendar'),

                        Forms\Components\TimePicker::make('heure_debut')
                            ->label('Heure de début')
                            ->required()
                            ->prefixIcon('heroicon-o-clock'),

                        Forms\Components\TimePicker::make('heure_fin')
                            ->label('Heure de fin')
                            ->required()
                            ->prefixIcon('heroicon-o-clock'),
                    ])
                    ->columns(3),
            ]);
    }

    // 📊 TABLE
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('classe.nom_classe')
                    ->label('Classe')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-academic-cap'),

                Tables\Columns\TextColumn::make('matiere.libelle')
                    ->label('Matière')
                    ->sortable()
                    ->icon('heroicon-o-book-open'),

                Tables\Columns\TextColumn::make('enseignant.name')
                    ->label('Enseignant')
                    ->sortable()
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('jour')
                    ->label('Jour')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('heure_debut')
                    ->label('Début')
                    ->icon('heroicon-o-clock'),

                Tables\Columns\TextColumn::make('heure_fin')
                    ->label('Fin')
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                SelectFilter::make('classe')->relationship('classe', 'nom_classe'),
                SelectFilter::make('matiere')->relationship('matiere', 'libelle'),
                SelectFilter::make('enseignant')->relationship('enseignant', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->requiresConfirmation(),
            ]);
    }

    // 👁️ INFOLIST
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Détails de l’emploi du temps')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('classe.nom_classe')
                            ->label('Classe')
                            ->icon('heroicon-o-academic-cap'),

                        TextEntry::make('matiere.libelle')
                            ->label('Matière')
                            ->icon('heroicon-o-book-open'),

                        TextEntry::make('enseignant.name')
                            ->label('Enseignant')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('jour')
                            ->label('Jour')
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('heure_debut')
                            ->label('Heure de début')
                            ->icon('heroicon-o-clock'),

                        TextEntry::make('heure_fin')
                            ->label('Heure de fin')
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmploiDuTemps::route('/'),
            'create' => Pages\CreateEmploiDuTemps::route('/create'),
            'edit' => Pages\EditEmploiDuTemps::route('/{record}/edit'),
        ];
    }
}
