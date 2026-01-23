<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParentEleveResource\Pages;
use App\Filament\Resources\ParentEleveResource\RelationManagers;
use App\Models\ParentEleve;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\RichEditor;

class ParentEleveResource extends Resource
{
    protected static ?string $model = ParentEleve::class;

    protected static ?string $navigationGroup = 'Scolarité';

    protected static ?string $navigationLabel = 'Parents';

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?int $navigationSort = 4;

    // 🔐 SÉCURITÉ
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Scolarite']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
              Forms\Components\Section::make('Informations personnelles')
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->label('Nom')
                            ->required(),
                        Forms\Components\TextInput::make('prenom')
                            ->label('Prénom')
                            ->required(),
                        Forms\Components\TextInput::make('telephone')
                            ->label('Téléphone')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        Forms\Components\RichEditor::make('adresse')
                            ->label('Adresse')
                            ->required()
                            ->placeholder('Exemple : Saisisez votre adresse correcte...')
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
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prenom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telephone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('adresse')
                    ->searchable(),
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

    

    public static function getRelations(): array
    {
        return [
            //
        ];
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