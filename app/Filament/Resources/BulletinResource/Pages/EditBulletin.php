<?php

namespace App\Filament\Resources\BulletinResource\Pages;

use App\Filament\Resources\BulletinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditBulletin extends EditRecord
{
    protected static string $resource = BulletinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Bulletin modifiée')
            ->body('Bulletin a été modifiée avec succès.');
    }
}
