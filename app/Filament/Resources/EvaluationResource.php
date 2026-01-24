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
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Carbon\Carbon;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static ?string $navigationGroup = 'Scolarité';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Evaluations';

    protected static ?string $pluralModelLabel = 'Evaluations';

    protected static ?int $navigationSort = 8;

    protected static ?string $recordTitleAttribute = 'type_evaluation';

     // 🔐 SÉCURITÉ
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Enseignant', 'Scolarite']);
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
        return 'Le nombre de type d\'évaluation';
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
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) =>
                        $state === 'examen' ? 'danger' :
                        ($state === 'devoir' ? 'warning' : 'info')
                    ),
                Tables\Columns\TextColumn::make('date_evaluation')
                    ->date()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('classe.nom_classe')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('matiere.libelle')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('annee.libelle')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type_evaluation')
                    ->options([
                        'devoir'=>'Devoir',
                        'interrogation'=>'Interrogation',
                        'examen'=>'Examen',
            ]),

            Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })

                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = Indicator::make('Created from ' . Carbon::parse($data['from'])->toFormattedDateString())
                                ->removeField('from');
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = Indicator::make('Created until ' . Carbon::parse($data['until'])->toFormattedDateString())
                                ->removeField('until');
                        }

                        return $indicators;
                    })
                
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

    // Infolist pour la vue detaillée

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detail sur l\'évaluation')
                ->schema([
                TextEntry::make('type_evaluation')
                    ->label('Type Evaluation'),
                TextEntry::make('date_evaluation')
                    ->label('Date Evaluation')
                    ->date(),
                TextEntry::make('classe.nom_classe')
                    ->label('Nom de la classe'),
                TextEntry::make('matiere.libelle')
                    ->label('Libelle de la matiere'),
                    TextEntry::make('annee.libelle')
                    ->label('Libelle Anneé'),
                ])->columns(3),
               
                
                
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