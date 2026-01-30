<?php

namespace App\Filament\Resources\BulletinResource\Pages;

use App\Filament\Resources\BulletinResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateBulletin extends CreateRecord
{
    protected static string $resource = BulletinResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Bulletin créée')
            ->body('Bulletin a été créée avec succès');
    }
}
