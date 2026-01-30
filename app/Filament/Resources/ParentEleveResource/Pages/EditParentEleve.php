<?php

namespace App\Filament\Resources\ParentEleveResource\Pages;

use App\Filament\Resources\ParentEleveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditParentEleve extends EditRecord
{
    protected static string $resource = ParentEleveResource::class;

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
            ->title('ParentEleve modifiée')
            ->body('Le parenteleve a été modifiée avec succès.');
    }
}