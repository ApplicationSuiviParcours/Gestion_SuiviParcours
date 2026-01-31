<?php

namespace App\Filament\Resources\NoteResource\Pages;

use App\Filament\Resources\NoteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateNote extends CreateRecord
{
    protected static string $resource = NoteResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Note créée')
            ->body('La note a été créée avec succès.');
    }

   protected function mutateFormDataBeforeCreate(array $data): array
{
    if (!isset($data['coefficient']) || $data['coefficient'] === null) {
        $data['coefficient'] = $this->getCoefficient($data);
    }

    return $data;
}

private function getCoefficient(array $data): int
{
    $bulletin = \App\Models\Bulletin::with('classe.matieres')
        ->find($data['bulletin_id']);

    $matiere = $bulletin?->classe
        ->matieres
        ->firstWhere('id', $data['matiere_id']);

    return (int) ($matiere?->pivot->coefficient ?? 1);
}


}