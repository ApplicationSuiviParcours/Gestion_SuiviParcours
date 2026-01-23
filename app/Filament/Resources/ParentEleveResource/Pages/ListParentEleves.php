<?php

namespace App\Filament\Resources\ParentEleveResource\Pages;

use App\Filament\Resources\ParentEleveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParentEleves extends ListRecords
{
    protected static string $resource = ParentEleveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
