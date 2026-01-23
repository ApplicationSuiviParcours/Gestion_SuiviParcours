<?php

namespace App\Filament\Resources\AnneeScolaireResource\Pages;

use App\Filament\Resources\AnneeScolaireResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnneeScolaires extends ListRecords
{
    protected static string $resource = AnneeScolaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
