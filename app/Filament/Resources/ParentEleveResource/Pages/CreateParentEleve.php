<?php

namespace App\Filament\Resources\ParentEleveResource\Pages;

use App\Filament\Resources\ParentEleveResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateParentEleve extends CreateRecord
{
    protected static string $resource = ParentEleveResource::class;
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('ParentEleve créer')
            ->body('ParentEleve  a été créer avec succès.');
    }
}