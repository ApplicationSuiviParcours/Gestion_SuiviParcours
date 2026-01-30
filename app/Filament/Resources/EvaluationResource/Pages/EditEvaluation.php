<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEvaluation extends EditRecord
{
    protected static string $resource = EvaluationResource::class;

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
            ->title('Evaluation modifiée')
            ->body('Evaluation modifiée avec succès.');
    }
}