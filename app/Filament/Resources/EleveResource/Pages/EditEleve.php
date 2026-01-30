<?php

namespace App\Filament\Resources\EleveResource\Pages;

use App\Filament\Resources\EleveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEleve extends EditRecord
{
    protected static string $resource = EleveResource::class;

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
            ->title('Eleve modifiée')
            ->body('Eleve a été modifiée avec succès');
    }
}