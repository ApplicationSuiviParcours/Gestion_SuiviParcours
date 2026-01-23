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

class EleveResource extends Resource
{
    protected static ?string $model = Eleve::class;

    protected static ?string $navigationGroup = 'Scolarité';

    protected static ?string $navigationLabel = 'Elèves';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'nom';

     // 🔐 SÉCURITÉ
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Scolarite']);
    }

    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make('matricule')
                            ->label('Matricule')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('nom')
                            ->label('Nom')
                            ->required(),
                        Forms\Components\TextInput::make('prenom')
                            ->label('Prénom')
                            ->required(),
                        Forms\Components\Select::make('genre')
                            ->label('Genre')
                            ->options(['M'=>'Masculin','F'=>'Féminin'])
                            ->required(),
                        Forms\Components\DatePicker::make('date_naissance')
                            ->label('Date de naissance')
                            ->required()
                            ->displayFormat('d/m/Y'),
                        Forms\Components\TextInput::make('lieu_naissance')
                            ->label('Lieu de naissance')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Coordonnées')
                    ->schema([
                        Forms\Components\TextInput::make('adresse')
                            ->label('Adresse')
                            ->required(),
                        Forms\Components\TextInput::make('telephone')
                            ->label('Téléphone')
                            ->tel()
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
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
                            ->default('actif'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matricule')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prenom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('genre')
                    ->badge()
                    ->color(fn ($state) => $state === 'M' ? 'info' : 'success'),
                Tables\Columns\TextColumn::make('date_naissance')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lieu_naissance')
                    ->searchable(),
                Tables\Columns\TextColumn::make('adresse')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telephone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('photo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('statut'),
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
                SelectFilter::make('genre')
                    ->options(['M'=>'Masculin','F'=>'Féminin']),
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