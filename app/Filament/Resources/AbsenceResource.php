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


class AbsenceResource extends Resource
{
    protected static ?string $model = Absence::class;

    protected static ?string $navigationGroup = 'Scolarité';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Absences';

    protected static ?string $pluralModelLabel = 'Absences';

    protected static ?int $navigationSort = 10;

    
    // 🔐 SÉCURITÉ
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Enseignant', 'Scolarite']);
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
                    ->required()
                    ->searchable(),

            Forms\Components\DatePicker::make('date_absence')
                ->required(),

            Forms\Components\RichEditor::make('motif')
                ->placeholder('Exemple : Maladie, Rendez-vous médical, Retard…')
                ->required()
                ->columnSpan('full')
                ->toolbarButtons([
                    'bold',        
                    'italic',      
                    'underline',   
                    'bulletList',  
                    'numberList',  
                    'link',        
                    'redo',        
                    'undo',        
                ]),

                Forms\Components\Toggle::make('justifie')
                    ->required(),
            ])
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('eleve.nom')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_absence')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('motif')
                    ->searchable(),
                Tables\Columns\IconColumn::make('justifie')
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
                //
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
                ->schema([
                TextEntry::make('eleve.nom')
                    ->label('Élève'),
                TextEntry::make('date_absence')
                    ->label('Date de l\'absence')
                    ->date(),
                TextEntry::make('motif')
                    ->label('Motif'),
                ])->columns(3),
                Section::make('Justification')
                ->schema([
                TextEntry::make('justifie')
                    ->label('Justifiée')
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