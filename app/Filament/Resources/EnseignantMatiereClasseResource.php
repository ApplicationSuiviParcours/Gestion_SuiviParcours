<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnseignantMatiereClasseResource\Pages;
use App\Models\EnseignantMatiereClasse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class EnseignantMatiereClasseResource extends Resource
{
    protected static ?string $model = EnseignantMatiereClasse::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Scolarité';

    protected static ?int $navigationSort = 11;

    protected static ?string $label = 'Affectation pédagogique';
    protected static ?string $pluralLabel = 'Affectations pédagogiques';

    /* 🔐 GESTION DES RÔLES */
    public static function canViewAny(): bool
    {
        return auth()->check() &&
            auth()->user()->hasAnyRole(['Administrateur', 'Scolarite']);
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
        return 'le nombre d\'élément';
    }


    /* 📝 FORMULAIRE */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Affectation')
                    ->icon('heroicon-o-link')
                    ->schema([
                        Forms\Components\Select::make('enseignant_id')
                            ->label('Enseignant')
                            ->relationship('enseignant', 'nom')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('matiere_id')
                            ->label('Matière')
                            ->relationship('matiere', 'libelle')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('classe_id')
                            ->label('Classe')
                            ->relationship('classe', 'nom_classe')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }

    /* 📊 TABLE */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('enseignant.nom')
                    ->label('Enseignant')
                    ->icon('heroicon-o-user')
                    ->searchable(),

                Tables\Columns\TextColumn::make('matiere.libelle')
                    ->label('Matière')
                    ->icon('heroicon-o-book-open'),

                Tables\Columns\TextColumn::make('classe.nom_classe')
                    ->label('Classe')
                    ->icon('heroicon-o-building-office'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('enseignant')
                    ->relationship('enseignant', 'nom')
                    ->label('Enseignant'),

                SelectFilter::make('matiere')
                    ->relationship('matiere', 'libelle')
                    ->label('Matière'),

                SelectFilter::make('classe')
                    ->relationship('classe', 'nom_classe')
                    ->label('Classe'),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /* 👁️ INFOLIST (PAGE VIEW) */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Détails de l’affectation')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('enseignant.nom')
                            ->label('Enseignant')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('matiere.libelle')
                            ->label('Matière')
                            ->icon('heroicon-o-book-open'),

                        TextEntry::make('classe.nom_classe')
                            ->label('Classe')
                            ->icon('heroicon-o-building-office'),
                    ])
                    ->columns(3),
            ]);
    }

    /* 📄 PAGES */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnseignantMatiereClasses::route('/'),
            'create' => Pages\CreateEnseignantMatiereClasse::route('/create'),
            // 'view' => Pages\ViewEnseignantMatiereClasse::route('/{record}'),
            'edit' => Pages\EditEnseignantMatiereClasse::route('/{record}/edit'),
        ];
    }
}
