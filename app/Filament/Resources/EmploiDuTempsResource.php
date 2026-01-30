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
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Emplois du Temps';
    protected static ?string $pluralModelLabel = 'Emplois du Temps';
    protected static ?string $navigationGroup = 'Scolarité';

    protected static ?int $navigationSort = 11;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails')
                    ->schema([
                        Forms\Components\Select::make('classe_id')
                            ->relationship('classe','nom_classe')
                            ->required(),
                        Forms\Components\Select::make('matiere_id')
                            ->relationship('matiere','libelle')
                            ->required(),
                        Forms\Components\Select::make('enseignant_id')
                            ->relationship('enseignant','name')
                            ->required(),
                        Forms\Components\Select::make('jour')
                            ->options([
                                'Lundi'=>'Lundi','Mardi'=>'Mardi','Mercredi'=>'Mercredi',
                                'Jeudi'=>'Jeudi','Vendredi'=>'Vendredi','Samedi'=>'Samedi'
                            ])
                            ->required(),
                        Forms\Components\TimePicker::make('heure_debut')->required(),
                        Forms\Components\TimePicker::make('heure_fin')->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('classe.nom_classe')->label('Classe')->sortable(),
                Tables\Columns\TextColumn::make('matiere.libelle')->label('Matière')->sortable(),
                Tables\Columns\TextColumn::make('enseignant.name')->label('Enseignant')->sortable(),
                Tables\Columns\TextColumn::make('jour')->sortable(),
                Tables\Columns\TextColumn::make('heure_debut')->label('Début'),
                Tables\Columns\TextColumn::make('heure_fin')->label('Fin'),
            ])
            ->filters([
                SelectFilter::make('classe')->relationship('classe','nom_classe'),
                SelectFilter::make('matiere')->relationship('matiere','libelle'),
                SelectFilter::make('enseignant')->relationship('enseignant','name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->requiresConfirmation(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Détails de l\'emploi du temps')
                    ->schema([
                        TextEntry::make('classe.nom_classe')->label('Classe'),
                        TextEntry::make('matiere.libelle')->label('Matière'),
                        TextEntry::make('enseignant.name')->label('Enseignant'),
                        TextEntry::make('jour')->label('Jour'),
                        TextEntry::make('heure_debut')->label('Heure début'),
                        TextEntry::make('heure_fin')->label('Heure fin'),
                    ])
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
