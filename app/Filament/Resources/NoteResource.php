<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteResource\Pages;
use App\Models\Note;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Carbon\Carbon;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Database\Eloquent\Builder;

class NoteResource extends Resource
{
    protected static ?string $model = Note::class;
    protected static ?string $navigationGroup = 'Scolarité';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Notes';
    protected static ?string $pluralModelLabel = 'Notes des élèves';
    protected static ?int $navigationSort = 10;
    protected static ?string $recordTitleAttribute = 'id';

    // 🔐 GESTION DES RÔLES
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
        $count = static::getModel()::count();
        return $count > 10 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Saisie de la note')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\Select::make('bulletin_id')
                            ->label('Bulletin')
                            ->relationship(
                                'bulletin',
                                'id',
                                fn ($query, callable $get) =>
                                    $query->where('eleve_id', $get('eleve_id'))
                            )
                            ->prefixIcon('heroicon-o-document-text')
                            ->searchable()
                            ->required()
                            ->disabled(fn (callable $get) => blank($get('eleve_id'))),

                        Forms\Components\Select::make('evaluation_id')
                            ->label('Évaluation')
                            ->relationship('evaluation','type_evaluation')
                            ->required()
                            ->rules([
                                fn (callable $get) => function (string $attribute, $value, $fail) use ($get) {
                                    $exists = \App\Models\Note::where('bulletin_id', $get('bulletin_id'))
                                        ->where('eleve_id', $get('eleve_id'))
                                        ->where('evaluation_id', $value)
                                        ->exists();

                                    if ($exists) {
                                        $fail('Cette note existe déjà pour cette évaluation.');
                                    }
                                }
                            ]),

                        Forms\Components\Select::make('eleve_id')
                            ->label('Élève')
                            ->relationship('eleve','nom')
                            ->prefixIcon('heroicon-o-user')
                            ->reactive()
                            ->required(),

                        Forms\Components\Select::make('matiere_id')
                            ->label('Matière')
                            ->relationship('matiere','libelle')
                            ->prefixIcon('heroicon-o-book-open')
                            ->required(),

                        Forms\Components\TextInput::make('valeur')
                            ->label('Note')
                            ->prefixIcon('heroicon-o-chart-bar')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('coefficient')
                            ->label('Coefficient')
                            ->prefixIcon('heroicon-o-calculator')
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
                Tables\Columns\TextColumn::make('bulletin.id')->label('Bulletin')->icon('heroicon-o-document-text')->sortable(),
                Tables\Columns\TextColumn::make('evaluation.type_evaluation')->label('Évaluation')->icon('heroicon-o-clipboard-document-check')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('eleve.nom')->label('Élève')->icon('heroicon-o-user')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('matiere.libelle')->label('Matière')->icon('heroicon-o-book-open')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('valeur')->label('Note')->icon('heroicon-o-chart-bar')
                    ->badge()
                    ->color(fn ($state) =>
                        $state < 10 ? 'danger' :
                        ($state < 14 ? 'warning' : 'success')
                    ),
                Tables\Columns\TextColumn::make('coefficient')->label('Coefficient')->icon('heroicon-o-calculator')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->icon('heroicon-o-calendar')->dateTime()->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->icon('heroicon-o-calendar')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('bulletin_id')->label('Bulletin')->relationship('bulletin', 'id'),
                SelectFilter::make('evaluation_id')->relationship('evaluation', 'type_evaluation')->searchable(),
                SelectFilter::make('eleve_id')->relationship('eleve','nom'),
                SelectFilter::make('matiere_id')->relationship('matiere','libelle'),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Créé depuis ' . Carbon::parse($data['created_from'])->toFormattedDateString())->removeField('created_from');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('Créé jusqu\'à ' . Carbon::parse($data['created_until'])->toFormattedDateString())->removeField('created_until');
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
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Détails de la note')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('bulletin.id')
                            ->label('Bulletin')
                            ->icon('heroicon-o-document-text'),

                        TextEntry::make('evaluation.type_evaluation')
                            ->label('Évaluation')
                            ->icon('heroicon-o-clipboard-document-check'),

                        TextEntry::make('eleve.nom')
                            ->label('Nom de l’élève')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('matiere.libelle')
                            ->label('Matière')
                            ->icon('heroicon-o-book-open'),

                        TextEntry::make('valeur')
                            ->label('Note obtenue')
                            ->icon('heroicon-o-academic-cap'),

                        TextEntry::make('coefficient')
                            ->label('Coefficient')
                            ->icon('heroicon-o-hashtag'),
                    ])
                    ->columns(3),

            ]);
    }

    public static function getRelations(): array
    {
        return [];
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
