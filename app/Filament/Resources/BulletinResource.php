<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BulletinResource\Pages;
use App\Models\Bulletin;
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

class BulletinResource extends Resource
{
    protected static ?string $model = Bulletin::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Bulletins';
    protected static ?string $pluralModelLabel = 'Bulletins';
    protected static ?string $navigationGroup = '';
    protected static ?int $navigationSort = 8;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails du Bulletin')
                    ->schema([
                        Forms\Components\Select::make('eleve_id')
                            ->relationship('eleve', 'nom')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('annee_id')
                            ->relationship('annee', 'libelle')
                            ->required(),
                        Forms\Components\TextInput::make('moyenne_generale')
                            ->label('Moyenne générale')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(20)
                            ->required(),
                        Forms\Components\RichEditor::make('appreciation')
                            ->label('Appréciation')
                            ->placeholder('Appréciation du professeur...')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'bulletList', 'numberList', 'link'
                            ])
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('eleve.nom')->label('Élève')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('annee.libelle')->label('Année')->sortable(),
                Tables\Columns\TextColumn::make('moyenne_generale')->label('Moyenne')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('eleve')->relationship('eleve','nom'),
                SelectFilter::make('annee')->relationship('annee','libelle'),
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
                Section::make('Détails du Bulletin')
                    ->schema([
                        TextEntry::make('eleve.nom')->label('Élève'),
                        TextEntry::make('annee.libelle')->label('Année'),
                        TextEntry::make('moyenne_generale')->label('Moyenne générale'),
                        TextEntry::make('appreciation')->label('Appréciation'),
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBulletins::route('/'),
            'create' => Pages\CreateBulletin::route('/create'),
            'edit' => Pages\EditBulletin::route('/{record}/edit'),
        ];
    }
}
