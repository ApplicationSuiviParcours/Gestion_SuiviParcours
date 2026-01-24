<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClasseResource\Pages;
use App\Filament\Resources\ClasseResource\RelationManagers;
use App\Models\Classe;
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
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Carbon\Carbon;


class ClasseResource extends Resource
{
    protected static ?string $model = Classe::class;
    protected static ?string $navigationGroup = 'Scolarité';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?int $navigationSort = 2;
    protected static ?string $pluralModelLabel = 'Classes';
    protected static ?string $navigationLabel = 'Classes';
    protected static ?string $recordTitleAttribute = 'nom_classe';

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
        return 'Le nombre de classe';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations sur la classe')
                    ->schema([
                        Forms\Components\TextInput::make('nom_classe')
                            ->label('Nom de la classe')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan('full'),

                        Forms\Components\Select::make('niveau')
                            ->label('Niveau')
                            ->options([
                                'prescolaire' => 'Préscolaire',
                                'primaire' => 'Primaire',
                                'college' => 'Collège',
                                'lycee' => 'Lycée',
                            ])
                            ->reactive()
                            ->required(),

                        Forms\Components\TextInput::make('filiere')
                            ->label('Filière')
                            ->visible(fn ($get) => $get('niveau') === 'lycee')
                            ->placeholder('Exemple : Science, Littéraire')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('effectif_max')
                            ->label('Effectif maximum')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(200)
                            ->suffix('élèves'),
                    ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom_classe')
                    ->label('Classe')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('niveau')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'prescolaire' => 'gray',
                        'primaire'    => 'success',
                        'college'     => 'warning',
                        'lycee'       => 'danger',
                        default       => 'secondary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('filiere')
                    ->searchable(),
                Tables\Columns\TextColumn::make('effectif_max')
                   ->counts('eleves')
                    ->label('Effectif')
                    ->badge()
                    ->color(function ($state) {
                        if ($state < 20) return 'success';   
                        if ($state < 40) return 'warning';   
                        return 'danger';                     
                    }),
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
                SelectFilter::make('niveau')
                    ->label('Niveau')
                    ->options([
                        'prescolaire' => 'Préscolaire',
                        'primaire'    => 'Primaire',
                        'college'     => 'Collège',
                        'lycee'       => 'Lycée',
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
                Section::make('Informations sur la classe')
                ->schema([
                TextEntry::make('nom_classe')
                    ->label('Nom de la classe'),
                TextEntry::make('niveau')
                    ->label('Niveau'),
                TextEntry::make('filiere')
                    ->label('Filière'),
                TextEntry::make('effectif_max')
                    ->label('Effectif maximum'),
                ])->columns(4),
                    
                
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
            'index' => Pages\ListClasses::route('/'),
            'create' => Pages\CreateClasse::route('/create'),
            'edit' => Pages\EditClasse::route('/{record}/edit'),
        ];
    }
}