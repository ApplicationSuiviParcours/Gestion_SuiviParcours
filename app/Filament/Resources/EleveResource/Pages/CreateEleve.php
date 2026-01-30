<?php

namespace App\Filament\Resources\EleveResource\Pages;

use App\Filament\Resources\EleveResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEleve extends CreateRecord
{
    protected static string $resource = EleveResource::class;

    protected function getCreatedNotification(): ?Notification
{
    return Notification::make()
        ->success()
        ->title('Nouvelle Eleve créée')
        ->body('Nouvelle Eleve à été créée avec succès.');
}
    
}