<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Classe;

class ClasseStats extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Classes', Classe::count()),
        ];
    }

    protected function getHeading(): ?string
    {
        return 'Statistiques des classes';
    }
}
