<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use App\Models\Parametre;
use Filament\Notifications\Notification;


class Parametres extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Paramètres';
    protected static ?string $navigationGroup = '⚙ Système';
    protected static ?int $navigationSort = 999; 
    protected static string $view = 'filament.pages.parametres';

    public $data = [];

    public function mount(): void
    {
        $this->data = Parametre::pluck('valeur', 'cle')->toArray();
    }

    public function save(): void
{
    foreach ($this->data as $cle => $valeur) {
        Parametre::where('cle', $cle)->update([
            'valeur' => $valeur
        ]);
    }

    Notification::make()
    ->title('Succès')
    ->body('Les paramètres ont été mis à jour avec succès.')
    ->success()
    ->duration(3000)
    ->send();

}


}

