<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsenceResource\Pages;
use App\Filament\Resources\AbsenceResource\RelationManagers;
use App\Models\Absence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\RichEditor;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Carbon\Carbon;
use Filament\Forms\Components\TextInput;


class AbsenceResource extends Resource
{
    protected static ?string $model = Absence::class;

    protected static ?string $navigationGroup = 'Scolarité';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Absences';

    protected static ?string $pluralModelLabel = 'Absences';

    protected static ?int $navigationSort = 1;

    
    // 🔐 SÉCURITÉ
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
        return static::getModel()::count() > 10 ? 'warning' : 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Le nombre des élèves en absence ou non';
    }

    public static function form(Form $form): Form
    {
        return $form
           ->schema([
            Forms\Components\Section::make('Informations sur l\'absence')
                ->schema([
                    Forms\Components\Select::make('eleve_id')
                        ->relationship('eleve', 'nom')
                        ->label('Élève')
                        ->prefixIcon('heroicon-o-user')
                        ->required()
                        ->searchable(),

                    Forms\Components\DatePicker::make('date_absence')
                        ->label('Date de l\'absence')
                        ->prefixIcon('heroicon-o-calendar-days')
                        ->required(),

                    Forms\Components\RichEditor::make('motif')
                        ->label('Motif de l\'absence')
                        ->placeholder('Exemple : Maladie, Rendez-vous médical, Retard…')
                        ->required()
                        ->columnSpan('full')
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'underline',
                            'strike',
                            'bulletList',
                            'numberList',
                            'link',
                            'redo',
                            'undo',
                        ]),

                    Forms\Components\Toggle::make('justifie')
                        ->label('Absence justifiée')
                        ->required(),
                ])
                ->columns(3),
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('eleve.nom')
                    ->icon('heroicon-o-user')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_absence')
                    ->icon('heroicon-o-calendar')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('motif')
                    ->icon('heroicon-o-book-open')
                    ->searchable(),
                Tables\Columns\IconColumn::make('justifie')
                    ->color(fn ($state) => $state === 'justifie' ? 'danger' : 'success')
                    ->icon(fn ($state) =>
                        $state === 'actif'
                            ? 'heroicon-o-check-circle'
                            : 'heroicon-o-x-circle'
                    ),
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
                SelectFilter::make('eleve_id')
                ->relationship('eleve', 'nom')
                ->searchable()
                ->label('Élève'),
                Filter::make('date_absence')
                ->form([
                    DatePicker::make('date_absence')
                        ->label('Date d\'absence'),
                ])
                ->query(function ($query, array $data) {
                    return $query->when(
                        $data['date_absence'],
                        fn ($q, $date) => $q->whereDate('date_absence', $date)
                    );
                }),
                SelectFilter::make('motif')
                ->options(
                    \App\Models\Absence::pluck('motif', 'motif')->toArray()
                )
                ->label('Motif Absence'),
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
            Section::make('Informations sur l\'absence')
                ->icon('heroicon-o-exclamation-triangle')
                ->schema([
                    TextEntry::make('eleve.nom')
                        ->label('Élève')
                        ->icon('heroicon-o-user'),

                    TextEntry::make('date_absence')
                        ->label('Date de l\'absence')
                        ->icon('heroicon-o-calendar-days')
                        ->date(),

                    TextEntry::make('motif')
                        ->label('Motif de l\'absence')
                        ->icon('heroicon-o-document-text'),
                ])
                ->columns(3),

            Section::make('Justification')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    TextEntry::make('justifie')
                        ->label('Absence justifiée')
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
            'index' => Pages\ListAbsences::route('/'),
            'create' => Pages\CreateAbsence::route('/create'),
            'edit' => Pages\EditAbsence::route('/{record}/edit'),
        ];
    }
}