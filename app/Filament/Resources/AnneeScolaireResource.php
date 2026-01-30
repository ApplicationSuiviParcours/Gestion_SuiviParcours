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
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Carbon\Carbon;

class AnneeScolaireResource extends Resource
{
    protected static ?string $model = AnneeScolaire::class;
    protected static ?string $navigationGroup = 'Scolarité';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?int $navigationSort = 2;
    protected static ?string $pluralModelLabel = 'Annee Scolaire';
    protected static ?string $recordTitleAttribute = 'actif';


    
    // 🔐 SÉCURITÉ
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Scolarite']);
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
        return static::getModel()::count() > 10 ? 'warning' : 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Le nombre d\'année entré';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations sur l\'année scolaire')
                    ->schema([
                    Forms\Components\TextInput::make('libelle')
                        ->label('Libellé de l\'année')
                        ->prefixIcon('heroicon-o-user')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('date_debut')
                        ->label('Date de début')
                        ->prefixIcon('heroicon-o-calendar')
                        ->required()
                        ->displayFormat('d/m/Y'),
                    Forms\Components\DatePicker::make('date_fin')
                        ->label('Date de fin')
                        ->prefixIcon('heroicon-o-calendar')
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
                    ->icon('heroicon-o-user')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_debut')
                    ->icon('heroicon-o-calendar')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_fin')
                    ->icon('heroicon-o-calendar')
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
                SelectFilter::make('libelle')
                ->options(
                    \App\Models\AnneeScolaire::pluck('libelle', 'libelle')->toArray()
                )
                ->label('Année scolaire'),
                Filter::make('date_debut')
                    ->form([
                        DatePicker::make('date_debut')
                            ->label('Date de début'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['date_debut'],
                            fn ($q, $date) => $q->whereDate('date_debut', $date)
                        );
                    }),
                Filter::make('date_fin')
                    ->form([
                        DatePicker::make('date_fin')
                            ->label('Date de fin'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['date_fin'],
                            fn ($q, $date) => $q->whereDate('date_fin', $date)
                        );
                    }),

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
            Section::make('Informations sur l\'année scolaire')
                ->icon('heroicon-o-calendar') 
                ->schema([
                    TextEntry::make('libelle')
                        ->label('Libellé de l\'année')
                        ->icon('heroicon-o-tag'),

                    TextEntry::make('date_debut')
                        ->label('Date de début')
                        ->icon('heroicon-o-calendar') 
                        ->date(),

                    TextEntry::make('date_fin')
                        ->label('Date de fin')
                        ->icon('heroicon-o-calendar') 
                        ->date(),
                ])
                ->columns(3),

            Section::make('Statut')
                ->icon('heroicon-o-check-circle')
                ->schema([
                    TextEntry::make('actif')
                        ->label('Année active ?')
                        ->icon(fn (bool $state) =>
                            $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'
                        )
                        ->badge()
                        ->color(fn (bool $state) => $state ? 'success' : 'danger')
                        ->formatStateUsing(fn (bool $state): string => $state ? 'Oui' : 'Non'),
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