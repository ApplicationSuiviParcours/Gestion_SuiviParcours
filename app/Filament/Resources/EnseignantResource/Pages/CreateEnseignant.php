<?php

namespace App\Filament\Resources\EnseignantResource\Pages;

use App\Filament\Resources\EnseignantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEnseignant extends CreateRecord
{
    protected static string $resource = EnseignantResource::class;

    protected function getCreatedNotification(): ?Notification
{
    return Notification::make()
        ->success()
        ->title('Enseignant créer')
        ->body('Enseignant a été créer avec succès');
}
}