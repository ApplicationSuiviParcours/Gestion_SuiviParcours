<?php



namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\UserStats;
use App\Filament\Widgets\ClasseStats;
use App\Filament\Widgets\EleveStats;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Tableau de bord';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            UserStats::class,
            ClasseStats::class,
            EleveStats::class,
        ];
    }

    
    // 🔐 SÉCURITÉ
    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['Administrateur', 'Enseignant', 'Scolarite']);
    }

}

