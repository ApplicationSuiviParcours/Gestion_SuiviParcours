<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoteResource\Pages;
use App\Models\Note;
use App\Models\ClasseMatiere;
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
        return auth()->user()->hasRole(['Administrateur', 'Scolarite', 'Enseignant']);
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
        return static::getModel()::count() > 10 ? 'warning' : 'success';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Saisie de la note')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\Select::make('eleve_id')
                            ->label('Élève')
                            ->relationship('eleve', 'nom')
                            ->prefixIcon('heroicon-o-user')
                            ->reactive()
                            ->required(),

                        Forms\Components\Select::make('bulletin_id')
                            ->label('Bulletin')
                            ->relationship('bulletin', 'id', fn ($query, callable $get) =>
                                $query->where('eleve_id', $get('eleve_id'))
                            )
                            ->prefixIcon('heroicon-o-document-text')
                            ->searchable()
                            ->required()
                            ->disabled(fn (callable $get) => blank($get('eleve_id'))),

                        Forms\Components\Select::make('evaluation_id')
                            ->label('Évaluation')
                            ->relationship('evaluation', 'type_evaluation')
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

                        Forms\Components\Select::make('matiere_id')
                            ->label('Matière')
                            ->relationship('matiere', 'libelle')
                            ->prefixIcon('heroicon-o-book-open')
                            ->reactive()
                            ->required(),

                        Forms\Components\TextInput::make('valeur')
                            ->label('Note')
                            ->prefixIcon('heroicon-o-chart-bar')
                            ->numeric()
                            ->required(),

                        // ✅ Coefficient automatique selon ClasseMatiere
                        Forms\Components\TextInput::make('coefficient')
                            ->label('Coefficient')
                            ->numeric()
                            ->disabled() // l'utilisateur ne peut pas modifier
                            ->dehydrated(true) // indispensable pour envoyer la valeur à la base
                            ->default(function (callable $get) {
                                $eleveId = $get('eleve_id');
                                $matiereId = $get('matiere_id');

                                if (!$eleveId || !$matiereId) return null;

                                $eleve = \App\Models\Eleve::with('classe')->find($eleveId);
                                if (!$eleve || !$eleve->classe) return null;

                                return ClasseMatiere::where('classe_id', $eleve->classe->id)
                                    ->where('matiere_id', $matiereId)
                                    ->value('coefficient');
                            })->prefixIcon('heroicon-o-hashtag'),

                        // ✅ Verrouillage après validation
                        // Forms\Components\Toggle::make('verrouille')
                        //     ->label('Verrouiller la note')
                        //     ->default(false)
                        //     ->inline(false)
                        //     ->helperText('Une fois verrouillée, la note ne pourra plus être modifiée.'),
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
                    ->color(fn ($state) => $state < 10 ? 'danger' : ($state < 14 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('coefficient')->label('Coefficient')->icon('heroicon-o-hashtag')->sortable(),
                // Tables\Columns\TextColumn::make('verrouille')->label('Verrouillée')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Date')->icon('heroicon-o-calendar')->dateTime()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('bulletin_id')->label('Bulletin')->relationship('bulletin', 'id'),
                SelectFilter::make('evaluation_id')->relationship('evaluation', 'type_evaluation')->searchable(),
                SelectFilter::make('eleve_id')->relationship('eleve','nom'),
                SelectFilter::make('matiere_id')->relationship('matiere','libelle'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->disabled(fn($record) => $record->verrouille),
                Tables\Actions\DeleteAction::make()->requiresConfirmation()->disabled(fn($record) => $record->verrouille),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // 🔹 Recalcul automatique après création ou modification
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotes::route('/'),
            'create' => Pages\CreateNote::route('/create', function($record) {
                if ($record && $record->bulletin) {
                    $record->bulletin->recalculerMoyenne();
                }
            }),
            'edit' => Pages\EditNote::route('/{record}/edit', function($record) {
                if ($record && $record->bulletin) {
                    $record->bulletin->recalculerMoyenne();
                }
            }),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Détails de la note')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('bulletin.id')->label('Bulletin')->icon('heroicon-o-document-text'),
                        TextEntry::make('evaluation.type_evaluation')->label('Évaluation')->icon('heroicon-o-clipboard-document-check'),
                        TextEntry::make('eleve.nom')->label('Nom de l’élève')->icon('heroicon-o-user'),
                        TextEntry::make('matiere.libelle')->label('Matière')->icon('heroicon-o-book-open'),
                        TextEntry::make('valeur')->label('Note obtenue')->icon('heroicon-o-academic-cap'),
                        TextEntry::make('coefficient')->label('Coefficient')->icon('heroicon-o-hashtag'),
                        TextEntry::make('verrouille')->label('Verrouillée'),
                    ])
                    ->columns(3),
            ]);
    }
}
