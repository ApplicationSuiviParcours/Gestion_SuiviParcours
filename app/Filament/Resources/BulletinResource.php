<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BulletinResource\Pages;
use App\Models\Bulletin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class BulletinResource extends Resource
{
    protected static ?string $model = Bulletin::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Bulletins';
    protected static ?string $pluralModelLabel = 'Bulletins';
    protected static ?string $navigationGroup = 'Scolarité';
    protected static ?int $navigationSort = 8;
    protected static ?string $recordTitleAttribute = 'id';

    // 🔐 GESTION DES RÔLES
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
        $count = static::getModel()::count();
        return $count > 10 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails du Bulletin')
                    ->schema([
                        Forms\Components\Select::make('eleve_id')
                            ->relationship('eleve', 'nom')
                            ->label('Élève')
                            ->required()
                            ->searchable()
                            ->prefixIcon('heroicon-o-user'),

                        Forms\Components\Select::make('classe_id')
                            ->relationship('classe', 'nom_classe')
                            ->label('Classe')
                            ->required()
                            ->searchable()
                            ->prefixIcon('heroicon-o-academic-cap'),

                        Forms\Components\Select::make('annee_id')
                            ->relationship('annee', 'libelle')
                            ->label('Année scolaire')
                            ->required()
                            ->searchable()
                            ->prefixIcon('heroicon-o-calendar'),

                        Forms\Components\Select::make('periode')
                            ->label('Periode')
                            ->options([
                                'Trimestre 1' => 'Trimestre 1',
                                'Trimestre 2' => 'Trimestre 2',
                                'Trimestre 3' => 'Trimestre 3',
                            ])
                            ->required()
                            ->prefixIcon('heroicon-o-academic-cap'),

                        Forms\Components\TextInput::make('moyenne')
                            ->label('Moyenne')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(20)
                            ->required()
                            ->prefixIcon('heroicon-o-chart-bar'),

                        Forms\Components\TextInput::make('rang')
                            ->label('Rang')
                            ->numeric()
                            ->required(false),
                            // ->prefixIcon('heroicon-o-clipboard-check'),

                        Forms\Components\RichEditor::make('appreciation')
                            ->label('Appréciation')
                            ->placeholder('Appréciation du professeur...')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'numberList', 'link'])
                            ->columnSpanFull()
                    ])
            ]);
    }

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
                    ->searchable()
                    ->icon('heroicon-o-academic-cap'),

                Tables\Columns\TextColumn::make('annee.libelle')
                    ->label('Année scolaire')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->badge()
                    ->searchable()
                    ->icon('heroicon-o-academic-cap'),

                Tables\Columns\TextColumn::make('moyenne')
                    ->label('Moyenne générale')
                    ->sortable()
                    ->icon('heroicon-o-chart-bar'),

                Tables\Columns\TextColumn::make('rang')
                    ->label('Rang')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->toggleable()
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime()
                    ->toggleable()
                    ->icon('heroicon-o-pencil'),
            ])
            ->filters([
                SelectFilter::make('eleve')->relationship('eleve','nom'),
                SelectFilter::make('classe')->relationship('classe','nom_classe'),
                SelectFilter::make('annee')->relationship('annee','libelle'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Détails du Bulletin')
                    ->schema([
                        TextEntry::make('eleve.nom')->label('Élève')->icon('heroicon-o-user'),
                        TextEntry::make('classe.nom_classe')->label('Classe')->icon('heroicon-o-academic-cap'),
                        TextEntry::make('annee.libelle')->label('Année scolaire')->icon('heroicon-o-calendar'),
                        TextEntry::make('periode')->label('Periode')->icon('heroicon-o-academic-cap'),
                        TextEntry::make('moyenne')->label('Moyenne générale')->icon('heroicon-o-chart-bar'),
                        TextEntry::make('rang')->label('Rang'),
                        TextEntry::make('appreciation')->label('Appréciation')->icon('heroicon-o-chat-bubble-bottom-center-text'),
                    ])->columns(3)
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBulletins::route('/'),
            'create' => Pages\CreateBulletin::route('/create'),
            'edit' => Pages\EditBulletin::route('/{record}/edit'),
        ];
    }
}
