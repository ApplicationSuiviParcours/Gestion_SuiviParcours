<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // 🔐 SÉCURITÉ : Seul super_admin peut éditer un utilisateur
    protected function authorizeAccess(): void
    {
        if (!auth()->user()->hasRole('Administrateur')) {
            abort(403);
        }
    }

    // 🔧 Action avant mise à jour (optionnel)
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ne modifie pas le mot de passe si vide
        if (empty($data['password'])) {
            unset($data['password']);
        }
        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Utilisateur modifié')
            ->body('Le compte utilisateur a été modifié avec succès.');
    }

    

}