<?php

namespace App\Filament\Resources\EmploiDuTempsResource\Pages;

use App\Filament\Resources\EmploiDuTempsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEmploiDuTemps extends EditRecord
{
    protected static string $resource = EmploiDuTempsResource::class;

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
            ->title('Emploi du Temps modifiée')
            ->body('Emploi du Temps a été modifiée avec succès');
    }
}
