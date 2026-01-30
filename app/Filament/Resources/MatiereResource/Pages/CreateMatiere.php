<?php

namespace App\Filament\Resources\MatiereResource\Pages;

use App\Filament\Resources\MatiereResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateMatiere extends CreateRecord
{
    protected static string $resource = MatiereResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Matiere créée')
            ->body('La matière a été créée avec succès.');
    }
}