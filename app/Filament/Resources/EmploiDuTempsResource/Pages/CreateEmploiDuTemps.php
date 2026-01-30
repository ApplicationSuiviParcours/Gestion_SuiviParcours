<?php

namespace App\Filament\Resources\EmploiDuTempsResource\Pages;

use App\Filament\Resources\EmploiDuTempsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEmploiDuTemps extends CreateRecord
{
    protected static string $resource = EmploiDuTempsResource::class;

    
        protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Emploi du Temps créée')
            ->body('Emploi du Temps a été créée avec succès.');
    }
}
