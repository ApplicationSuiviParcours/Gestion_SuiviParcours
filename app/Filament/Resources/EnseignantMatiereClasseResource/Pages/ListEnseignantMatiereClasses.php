<?php

namespace App\Filament\Resources\EnseignantMatiereClasseResource\Pages;

use App\Filament\Resources\EnseignantMatiereClasseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEnseignantMatiereClasses extends ListRecords
{
    protected static string $resource = EnseignantMatiereClasseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
