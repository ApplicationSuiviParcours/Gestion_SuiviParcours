<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationResource\Pages;
use App\Filament\Resources\EvaluationResource\RelationManagers;
use App\Models\Evaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static ?string $navigationGroup = 'Scolarité';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Evaluations';

    protected static ?int $navigationSort = 8;

     // 🔐 SÉCURITÉ
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Enseignant', 'Scolarite']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Section::make('Informations sur l\'évaluation')
                        ->schema([
                            Forms\Components\Select::make('type_evaluation')
                                ->options(['devoir'=>'Devoir','examen'=>'Examen'])
                                ->required(),

                            Forms\Components\DatePicker::make('date_evaluation')
                                ->required(),

                            Forms\Components\Select::make('classe_id')
                                ->relationship('classe','nom_classe')
                                ->required(),

                            Forms\Components\Select::make('matiere_id')
                                ->relationship('matiere','libelle')
                                ->required(),

                            Forms\Components\Select::make('annee_id')
                                ->relationship('annee','libelle')
                                ->required(),
                        ])->columns(2) 
                ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type_evaluation')
                    ->badge()
                    ->color(fn ($state) =>
                        $state === 'examen' ? 'danger' :
                        ($state === 'devoir' ? 'warning' : 'info')
                    ),
                Tables\Columns\TextColumn::make('date_evaluation')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('classe_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('matiere_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('annee_id')
                    ->numeric()
                    ->sortable(),
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
                SelectFilter::make('type')
                    ->options([
                        'devoir'=>'Devoir',
                        'interrogation'=>'Interrogation',
                        'examen'=>'Examen',
            ])
                
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
            'index' => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluation::route('/create'),
            'edit' => Pages\EditEvaluation::route('/{record}/edit'),
        ];
    }
}