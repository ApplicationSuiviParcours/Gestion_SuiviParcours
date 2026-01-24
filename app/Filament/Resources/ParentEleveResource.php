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

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $pluralModelLabel = 'Parents';

     protected static ?string $recordTitleAttribute = 'nom';
    
    protected static ?int $navigationSort = 4;

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
        return 'Le nombre de parent d\'élève';
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
                SelectFilter::make('nom')
                ->options(
                    \App\Models\ParentEleve::pluck('nom', 'nom')->toArray()
                )
                ->label('Nom de parent'),
                SelectFilter::make('prenom')
                    ->options(
                        \App\Models\ParentEleve::pluck('prenom', 'prenom')->toArray()
                    )
                    ->label('Prenom de parent'),
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
                Section::make('Detail sur l\'évaluation')
                ->schema([
                TextEntry::make('nom')
                    ->label('Nom Parent'),
                TextEntry::make('prenom')
                    ->label('Prenom Parent'),
                TextEntry::make('telephone')
                    ->label('Telephone'),
                TextEntry::make('email')
                    ->label('Email Parent'),
                TextEntry::make('adresse')
                    ->label('Adresse Parent'),
                ])->columns(3),
               
                
                
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