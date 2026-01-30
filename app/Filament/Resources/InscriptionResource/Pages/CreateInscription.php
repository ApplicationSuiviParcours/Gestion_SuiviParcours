<?php

namespace App\Filament\Resources\InscriptionResource\Pages;

use App\Filament\Resources\InscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateInscription extends CreateRecord
{
    protected static string $resource = InscriptionResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Inscription enregistrée')
            ->body('L\'inscription enregistrée avec succès.');
    }
}