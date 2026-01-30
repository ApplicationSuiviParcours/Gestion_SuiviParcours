<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EleveResource\Pages;
use App\Filament\Resources\EleveResource\RelationManagers;
use App\Models\Eleve;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Carbon\Carbon;

class EleveResource extends Resource
{
    protected static ?string $model = Eleve::class;

    protected static ?string $navigationGroup = 'Scolarité';

    protected static ?string $navigationLabel = 'Elèves';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 4;
    protected static ?string $pluralModelLabel = 'Elèves';
    protected static ?string $recordTitleAttribute = 'nom';

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
        return 'Le nombre d\'élève';
    }
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make('matricule')
                            ->label('Matricule')
                            ->prefixIcon('heroicon-o-identification')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('nom')
                            ->label('Nom')
                            ->prefixIcon('heroicon-o-user')
                            ->required(),
                        Forms\Components\TextInput::make('prenom')
                            ->label('Prénom')
                            ->prefixIcon('heroicon-o-user')
                            ->required(),
                        Forms\Components\Select::make('genre')
                            ->label('Genre')
                            ->options(['M'=>'Masculin','F'=>'Féminin'])
                            ->prefixIcon('heroicon-o-user-group')
                            ->required(),
                        Forms\Components\DatePicker::make('date_naissance')
                            ->label('Date de naissance')
                            ->prefixIcon('heroicon-o-calendar')
                            ->required()
                            ->displayFormat('d/m/Y'),
                        Forms\Components\TextInput::make('lieu_naissance')
                            ->label('Lieu de naissance')
                            ->prefixIcon('heroicon-o-map')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Coordonnées')
                    ->schema([
                        Forms\Components\TextInput::make('adresse')
                            ->label('Adresse')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->required(),
                        Forms\Components\TextInput::make('telephone')
                            ->label('Téléphone')
                            ->tel()
                            ->prefixIcon('heroicon-o-phone')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->prefixIcon('heroicon-o-envelope')
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Photo de l\'élève')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->label('Photo')
                            ->image()
                            ->directory('eleves'),
                    ])->columns(1),

                Forms\Components\Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('statut')
                            ->label('Statut')
                            ->options(['actif'=>'Actif','inactif'=>'Inactif'])
                            ->prefixIcon('heroicon-o-check-circle')
                            ->default('actif'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matricule')
                    ->icon('heroicon-o-identification')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nom')
                    ->icon('heroicon-o-user')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prenom')
                    ->icon('heroicon-o-user')
                    ->searchable(),
                Tables\Columns\TextColumn::make('genre')
                    ->icon('heroicon-o-user-group')
                    ->badge()
                    ->color(fn ($state) => $state === 'M' ? 'info' : 'success'),
                Tables\Columns\TextColumn::make('date_naissance')
                    ->icon('heroicon-o-calendar')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lieu_naissance')
                    ->icon('heroicon-o-map')
                    ->searchable(),
                Tables\Columns\TextColumn::make('adresse')
                    ->icon('heroicon-o-map-pin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telephone')
                    ->icon('heroicon-o-phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-o-envelope')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Photo')
                    ->disk('public') 
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn ($state) => $state === 'actif' ? 'success' : 'danger')
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
                SelectFilter::make('nom')
                ->options(
                    \App\Models\Eleve::pluck('nom', 'nom')->toArray()
                ),
                SelectFilter::make('prenom')
                ->options(
                    \App\Models\Eleve::pluck('prenom', 'prenom')->toArray()
                )
                ->label('Prenom Elève'),
                SelectFilter::make('genre')
                    ->options(['M'=>'Masculin','F'=>'Féminin']),

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

            Section::make('Informations générales')
                ->icon('heroicon-o-user')
                ->schema([
                    TextEntry::make('matricule')
                        ->label('Matricule'),

                    TextEntry::make('nom')
                        ->label('Nom'),

                    TextEntry::make('prenom')
                        ->label('Prénom'),

                    TextEntry::make('genre')
                        ->label('Genre')
                        ->formatStateUsing(
                            fn (string $state) => $state === 'M' ? 'Masculin' : 'Féminin'
                        ),

                    TextEntry::make('date_naissance')
                        ->label('Date de naissance')
                        ->date('d/m/Y'),

                    TextEntry::make('lieu_naissance')
                        ->label('Lieu de naissance'),
                ])
                ->columns(2),

            Section::make('Coordonnées')
                ->icon('heroicon-o-phone')
                ->schema([
                    TextEntry::make('adresse')
                        ->label('Adresse'),

                    TextEntry::make('telephone')
                        ->label('Téléphone'),

                    TextEntry::make('email')
                        ->label('Email'),
                ])
                ->columns(3),

            Section::make('Photo de l\'élève')
                ->schema([
                    ImageEntry::make('photo')
                        ->label('')
                        ->disk('public')
                        ->height(150)
                        ->circular(),
                ]),

            Section::make('Statut')
                ->schema([
                    TextEntry::make('statut')
                        ->label('Statut')
                        ->badge()
                        ->color(fn (string $state) =>
                            $state === 'actif' ? 'success' : 'danger'
                        )
                        ->formatStateUsing(fn (string $state) =>
                            ucfirst($state)
                        ),
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
            'index' => Pages\ListEleves::route('/'),
            'create' => Pages\CreateEleve::route('/create'),
            'edit' => Pages\EditEleve::route('/{record}/edit'),
        ];
    }
}