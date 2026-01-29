<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Eleve;

class EleveStats extends BaseWidget
{
     protected function getCards(): array
    {
        return [
            Card::make('Total Élèves', Eleve::count()),
        ];
    }

    protected function getHeading(): ?string
    {
        return 'Statistiques des élèves';
    }
}
