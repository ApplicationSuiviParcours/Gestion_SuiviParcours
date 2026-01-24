<?php

namespace App\Filament\Resources\EnseignantMatiereClasseResource\Pages;

use App\Filament\Resources\EnseignantMatiereClasseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEnseignantMatiereClasse extends EditRecord
{
    protected static string $resource = EnseignantMatiereClasseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Elements modifié ')
            ->body('Les Eléments  modifier avec succès.');
    }
}
