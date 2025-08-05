<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\ProjectStats;
use App\Filament\Widgets\ActiveProjectsChart;
use App\Filament\Widgets\TotalRevenueChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            ProjectStats::class,
            ActiveProjectsChart::class,
            TotalRevenueChart::class,
        ];
    }
}
