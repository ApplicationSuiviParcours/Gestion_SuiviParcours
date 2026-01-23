<?php

namespace App\Filament\Resources\EnseignantResource\Pages;

use App\Filament\Resources\EnseignantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEnseignant extends EditRecord
{
    protected static string $resource = EnseignantResource::class;

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
            ->title('Enseignant modifier')
            ->body('Enseignant a été modifier avec succès');
    }
}