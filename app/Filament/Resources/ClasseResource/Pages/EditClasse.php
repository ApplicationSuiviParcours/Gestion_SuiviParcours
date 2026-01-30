<?php

namespace App\Filament\Resources\ClasseResource\Pages;

use App\Filament\Resources\ClasseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditClasse extends EditRecord
{
    protected static string $resource = ClasseResource::class;

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
            ->title('Classe modifiée')
            ->body('Classe a été modifiée avec succès');
    }
}