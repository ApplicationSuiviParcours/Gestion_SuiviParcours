<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    // 🔐 SÉCURITÉ : Seul super_admin peut créer un utilisateur
    protected function authorizeAccess(): void
    {
        if (!auth()->user()->hasRole('Administrateur')) {
            abort(403);
        }
    }



    // 🔧 Action après création (optionnel)
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Mot de passe déjà hashé via UserResource
        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Utilisateur créé')
            ->body('Le compte utilisateur a été créé avec succès.');
    }
}