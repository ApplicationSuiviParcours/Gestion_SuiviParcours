<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteResource\Pages;
use App\Filament\Resources\NoteResource\RelationManagers;
use App\Models\Note;
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

class NoteResource extends Resource
{
    protected static ?string $model = Note::class;

    protected static ?string $navigationGroup = 'Scolarité';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Notes';

    protected static ?string $pluralModelLabel = 'Notes des élèves';

    protected static ?int $navigationSort = 9;

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
        return 'Le nombre de note';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Saisie de la note')
                ->icon('heroicon-o-pencil-square')
                ->schema([
                    Forms\Components\Select::make('evaluation_id')
                        ->label('Évaluation')
                        ->relationship('evaluation','type_evaluation')
                        ->required(),

                    Forms\Components\Select::make('eleve_id')
                        ->label('Élève')
                        ->relationship('eleve','nom')
                        ->required(),

                    Forms\Components\TextInput::make('valeur')
                        ->label('Note')
                        ->numeric()
                        ->required(),
                ])
                ->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('evaluation.type_evaluation')
            ->label('Évaluation')
            ->icon('heroicon-o-clipboard-document-check')
            ->searchable()
            ->sortable(),

            Tables\Columns\TextColumn::make('eleve.nom')
                ->label('Élève')
                ->icon('heroicon-o-user')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('valeur')
                ->label('Note')
                ->icon('heroicon-o-chart-bar')
                ->badge()
                ->color(fn ($state) =>
                    $state < 10 ? 'danger' :
                    ($state < 14 ? 'warning' : 'success')
                ),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->icon('heroicon-o-calendar')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->icon('heroicon-o-calendar')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('evaluation_id')
                    ->relationship('evaluation', 'type_evaluation')
                    ->searchable(),

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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Détails de la note')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('evaluation.type_evaluation')
                            ->label('Type d’évaluation')
                            ->icon('heroicon-o-clipboard-document-check'),

                        TextEntry::make('eleve.nom')
                            ->label('Nom de l’élève')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('valeur')
                            ->label('Note obtenue')
                            ->icon('heroicon-o-academic-cap'),
                    ])
                    ->columns(3),
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
            'index' => Pages\ListNotes::route('/'),
            'create' => Pages\CreateNote::route('/create'),
            'edit' => Pages\EditNote::route('/{record}/edit'),
        ];
    }
}