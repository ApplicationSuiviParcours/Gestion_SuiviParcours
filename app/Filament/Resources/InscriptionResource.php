<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InscriptionResource\Pages;
use App\Filament\Resources\InscriptionResource\RelationManagers;
use App\Models\Inscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;

class InscriptionResource extends Resource
{
    protected static ?string $model = Inscription::class;
    protected static ?string $navigationGroup = 'Scolarité';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Inscriptions';
    protected static ?int $navigationSort = 7;

    // 🔐 SÉCURITÉ
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Scolarite']);
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Inscription')
                    ->schema([
                        Forms\Components\Select::make('eleve_id')
                            ->relationship('eleve', 'nom')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('classe_id')
                            ->relationship('classe', 'nom_classe')
                            ->required(),
                        Forms\Components\Select::make('annee_id')
                            ->relationship('annee', 'libelle')
                            ->required(),
                        Forms\Components\DatePicker::make('date_inscription')
                            ->required(),
                        Forms\Components\TextInput::make('statut')
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('eleve.nom')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('classe.nom_classe')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('annee.libelle')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_inscription')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('statut')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
             ->filters([
                SelectFilter::make('classe_id')->relationship('classe','nom_classe'),
                SelectFilter::make('annee_id')->relationship('annee','libelle'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    
    public static function getRelations(): array
    {
        return [
            //
        ];
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