<?php

namespace App\Filament\Resources\ClasseResource\Pages;

use App\Filament\Resources\ClasseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateClasse extends CreateRecord
{
    protected static string $resource = ClasseResource::class;

     protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Nouvelle Classe créer')
            ->body('Classe a été créer avec succès');
    }
}