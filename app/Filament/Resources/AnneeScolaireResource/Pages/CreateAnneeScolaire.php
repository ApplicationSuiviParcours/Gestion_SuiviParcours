<?php

namespace App\Filament\Resources\AnneeScolaireResource\Pages;

use App\Filament\Resources\AnneeScolaireResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateAnneeScolaire extends CreateRecord
{
    protected static string $resource = AnneeScolaireResource::class;

    
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Année scolaire créée')
            ->body('L\'année scolaire a été créée avec succès');
    }
}