<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnseignantResource\Pages;
use App\Models\Enseignant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Carbon\Carbon;

class EnseignantResource extends Resource
{
    protected static ?string $model = Enseignant::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Scolarité';
    protected static ?string $navigationLabel = 'Enseignants';
    protected static ?string $pluralModelLabel = 'Enseignants';
    protected static ?int $navigationSort = 5;

    // 🔐 Sécurité
    public static function canViewAny(): bool
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
        return 'Nombre total d\'enseignants';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations Personnelles')
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->prefixIcon('heroicon-o-user'),

                        Forms\Components\TextInput::make('prenom')
                            ->label('Prénom')
                            ->required()
                            ->prefixIcon('heroicon-o-user'),

                        Forms\Components\TextInput::make('specialite')
                            ->label('Spécialité')
                            ->required()
                            ->prefixIcon('heroicon-o-academic-cap'),

                        Forms\Components\TextInput::make('telephone')
                            ->label('Téléphone')
                            ->tel()
                            ->required()
                            ->prefixIcon('heroicon-o-phone'),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->prefixIcon('heroicon-o-envelope')
                            ->columnSpan('full'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->icon('heroicon-o-user')
                    ->searchable(),

                Tables\Columns\TextColumn::make('prenom')
                    ->label('Prénom')
                    ->icon('heroicon-o-user')
                    ->searchable(),

                Tables\Columns\TextColumn::make('specialite')
                    ->label('Spécialité')
                    ->icon('heroicon-o-academic-cap')
                    ->searchable(),

                Tables\Columns\TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->icon('heroicon-o-phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Mis à jour le')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('nom')
                    ->options(\App\Models\Enseignant::whereNotNull('nom')->pluck('nom', 'nom')->toArray())
                    ->label('Nom Enseignant'),

                SelectFilter::make('prenom')
                    ->options(\App\Models\Enseignant::whereNotNull('prenom')->pluck('prenom', 'prenom')->toArray())
                    ->label('Prénom Enseignant'),

                SelectFilter::make('specialite')
                    ->options(\App\Models\Enseignant::whereNotNull('specialite')->pluck('specialite', 'specialite')->toArray())
                    ->label('Spécialité'),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Depuis ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('Jusqu\'à ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->requiresConfirmation(),
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
                Section::make('Informations personnelles')
                    ->schema([
                        TextEntry::make('nom')
                            ->label('Nom')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('prenom')
                            ->label('Prénom')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('specialite')
                            ->label('Spécialité')
                            ->icon('heroicon-o-academic-cap'),

                        TextEntry::make('telephone')
                            ->label('Téléphone')
                            ->icon('heroicon-o-phone'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope'),
                    ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnseignants::route('/'),
            'create' => Pages\CreateEnseignant::route('/create'),
            'edit' => Pages\EditEnseignant::route('/{record}/edit'),
        ];
    }
}
