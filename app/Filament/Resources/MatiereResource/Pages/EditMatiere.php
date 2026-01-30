<?php

namespace App\Filament\Resources\MatiereResource\Pages;

use App\Filament\Resources\MatiereResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditMatiere extends EditRecord
{
    protected static string $resource = MatiereResource::class;

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
            ->title('Matiere modfiée')
            ->body('La matière a été modifiée avec succès.');
    }
}