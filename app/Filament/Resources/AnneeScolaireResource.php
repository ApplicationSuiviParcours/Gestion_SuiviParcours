<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnneeScolaireResource\Pages;
use App\Filament\Resources\AnneeScolaireResource\RelationManagers;
use App\Models\AnneeScolaire;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class AnneeScolaireResource extends Resource
{
    protected static ?string $model = AnneeScolaire::class;
    protected static ?string $navigationGroup = 'Scolarité';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'actif';


    
    // 🔐 SÉCURITÉ
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Scolarite']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations sur l\'année scolaire')
    ->schema([
                    Forms\Components\TextInput::make('libelle')
                        ->label('Libellé de l\'année')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('date_debut')
                        ->label('Date de début')
                        ->required()
                        ->displayFormat('d/m/Y'),
                    Forms\Components\DatePicker::make('date_fin')
                        ->label('Date de fin')
                        ->required()
                        ->displayFormat('d/m/Y'),
                    Forms\Components\Toggle::make('actif')
                        ->label('Année active ?')
                        ->required(),
                ])
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('libelle')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_debut')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_fin')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('actif')
                    ->boolean(),
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
                Filter::make('actif')
                    ->query(fn ($q) => $q->where('actif', true)),
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
            'index' => Pages\ListAnneeScolaires::route('/'),
            'create' => Pages\CreateAnneeScolaire::route('/create'),
            'edit' => Pages\EditAnneeScolaire::route('/{record}/edit'),
        ];
    }
}