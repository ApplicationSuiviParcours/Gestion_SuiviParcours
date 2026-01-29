<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatiereResource\Pages;
use App\Models\Matiere;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Carbon\Carbon;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class MatiereResource extends Resource
{
    protected static ?string $model = Matiere::class;

    protected static ?string $navigationGroup = 'Scolarité';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Matières';
    protected static ?string $pluralModelLabel = 'Matières';
    protected static ?int $navigationSort = 6;
    protected static ?string $recordTitleAttribute = 'libelle';

    // 🔐 SÉCURITÉ
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
        return 'Le nombre de matières';
    }

    /* =========================
        FORMULAIRE
    ========================== */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Création de la matière')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\TextInput::make('libelle')
                            ->label('Libellé')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-book-open'),

                        Forms\Components\TextInput::make('coefficient')
                            ->label('Coefficient')
                            ->required()
                            ->numeric()
                            ->prefixIcon('heroicon-o-calculator'),
                    ])->columns(2),
            ]);
    }

    /* =========================
        TABLE
    ========================== */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('libelle')
                    ->label('Matière')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-book-open'),

                Tables\Columns\TextColumn::make('coefficient')
                    ->label('Coefficient')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-calculator'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Mis à jour le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                SelectFilter::make('libelle')
                    ->label('Libellé')
                    ->options(
                        Matiere::query()->pluck('libelle', 'libelle')->toArray()
                    ),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Depuis'),
                        DatePicker::make('created_until')->label('Jusqu\'à'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'] ?? null,
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'] ?? null,
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make(
                                'Depuis ' . Carbon::parse($data['created_from'])->toFormattedDateString()
                            )->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make(
                                'Jusqu\'à ' . Carbon::parse($data['created_until'])->toFormattedDateString()
                            )->removeField('created_until');
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

    /* =========================
        INFOLIST
    ========================== */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Détails de la matière')
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        TextEntry::make('libelle')
                            ->label('Libellé')
                            ->icon('heroicon-o-book-open'),

                        TextEntry::make('coefficient')
                            ->label('Coefficient')
                            ->icon('heroicon-o-calculator'),
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
            'index' => Pages\ListMatieres::route('/'),
            'create' => Pages\CreateMatiere::route('/create'),
            'edit' => Pages\EditMatiere::route('/{record}/edit'),
        ];
    }
}
