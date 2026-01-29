<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\User;

class UserStats extends BaseWidget
{
     protected function getCards(): array
    {
        return [
            Card::make('Total Utilisateurs', User::count()),
        ];
    }

    // Optionnel : pour afficher un titre au widget
    protected function getHeading(): ?string
    {
        return 'Statistiques des utilisateurs';
    }
}
