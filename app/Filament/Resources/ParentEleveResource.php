<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParentEleveResource\Pages;
use App\Models\ParentEleve;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Carbon\Carbon;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ParentEleveResource extends Resource
{
    protected static ?string $model = ParentEleve::class;

    protected static ?string $navigationGroup = 'Scolarité';
    protected static ?string $navigationLabel = 'Parents';
    protected static ?string $pluralModelLabel = 'Parents';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $recordTitleAttribute = 'nom';
    protected static ?int $navigationSort = 12;

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
        return 'Le nombre de parents d\'élèves';
    }

    /* =========================
        FORMULAIRE
    ========================== */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations personnelles')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->prefixIcon('heroicon-o-user'),

                        Forms\Components\TextInput::make('prenom')
                            ->label('Prénom')
                            ->required()
                            ->prefixIcon('heroicon-o-user'),

                        Forms\Components\TextInput::make('telephone')
                            ->label('Téléphone')
                            ->required()
                            ->prefixIcon('heroicon-o-phone'),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->prefixIcon('heroicon-o-envelope'),

                        RichEditor::make('adresse')
                            ->label('Adresse')
                            ->required()
                            ->columnSpan('full')
                            ->placeholder('Exemple : Saisissez votre adresse correcte...')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'numberList',
                                'link',
                                'undo',
                                'redo',
                            ]),
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
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('prenom')
                    ->label('Prénom')
                    ->searchable()
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->searchable()
                    ->icon('heroicon-o-phone'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope'),

                Tables\Columns\TextColumn::make('adresse')
                    ->label('Adresse')
                    ->limit(40)
                    ->wrap()
                    ->icon('heroicon-o-map-pin'),

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
                SelectFilter::make('nom')
                    ->label('Nom du parent')
                    ->options(
                        ParentEleve::query()->pluck('nom', 'nom')->toArray()
                    ),

                SelectFilter::make('prenom')
                    ->label('Prénom du parent')
                    ->options(
                        ParentEleve::query()->pluck('prenom', 'prenom')->toArray()
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
        INFOLIST (VIEW)
    ========================== */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informations du parent')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        TextEntry::make('nom')
                            ->label('Nom')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('prenom')
                            ->label('Prénom')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('telephone')
                            ->label('Téléphone')
                            ->icon('heroicon-o-phone'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope'),

                        TextEntry::make('adresse')
                            ->label('Adresse')
                            ->icon('heroicon-o-map-pin'),
                    ])->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParentEleves::route('/'),
            'create' => Pages\CreateParentEleve::route('/create'),
            'edit' => Pages\EditParentEleve::route('/{record}/edit'),
        ];
    }
}
