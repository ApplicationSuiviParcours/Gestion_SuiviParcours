<?php

namespace App\Filament\Resources\EnseignantMatiereClasseResource\Pages;

use App\Filament\Resources\EnseignantMatiereClasseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEnseignantMatiereClasse extends CreateRecord
{
    protected static string $resource = EnseignantMatiereClasseResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Elements créée ')
            ->body('Les Eléments  créée avec succès.');
    }
}
